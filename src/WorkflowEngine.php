<?php

declare(strict_types=1);

namespace Lemonade\Workflow;

use Carbon\CarbonInterval;
use Lemonade\Workflow\DataStorage\Log\Event\Event;
use Lemonade\Workflow\DataStorage\Log\Event\NullEvent;
use Lemonade\Workflow\DataStorage\Log\Event\SignalActivated;
use Lemonade\Workflow\DataStorage\Log\Event\SignalQueried;
use Lemonade\Workflow\DataStorage\Log\Event\TaskCompleted;
use Lemonade\Workflow\DataStorage\Log\Event\TaskErrored;
use Lemonade\Workflow\DataStorage\Log\Event\TaskFailed;
use Lemonade\Workflow\DataStorage\Log\Event\TaskStarted;
use Lemonade\Workflow\DataStorage\Log\Event\TimerActivated;
use Lemonade\Workflow\DataStorage\Signal;
use Lemonade\Workflow\DataStorage\Task;
use Lemonade\Workflow\DataStorage\Timer;
use Lemonade\Workflow\DataStorage\Workflow;
use Lemonade\Workflow\DataStorage\WorkflowRepositoryInterface;
use Lemonade\Workflow\Enum\TaskStatus;
use Lemonade\Workflow\Graph\DagBuilder;
use Lemonade\Workflow\Graph\Node;
use Psr\Container\ContainerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Throwable;

use function React\Async\async;
use function React\Promise\resolve;

/**
 * Is responsible for running a workflow.
 *  - It iterates over the graph and executes tasks
 *  - It persists the workflow state
 *  - It handles signals
 *  - It handles timers
 *  - It handles errors
 *  - It handles retries
 *  - It handles timeouts
 *
 * @internal
 *
 * @phpstan-import-type DagTypes from DagBuilder
 *
 */
#[AsMessageHandler]
class WorkflowEngine
{
    public function __construct(
        private readonly ContainerInterface $locator,
        private readonly WorkflowRepositoryInterface $workflowRepository,
    ) {
    }

    public function __invoke(Workflow $workflow): void
    {
        // iterate over graph and execute tasks
        $start = $workflow->graph->start();

        $this->walk($workflow, $start);
    }

    /**
     * @param Node<DagTypes> $current
     */
    private function walk(Workflow $workflow, Node $current): void
    {
        if ($current->item instanceof Task) {
            $result = $this->runTask($workflow, $current->item);
            if (!$result instanceof NullEvent) {
                $workflow->logs->add($result);
            }
        }

        if ($current->item instanceof Signal) {
            $result = $this->checkSignal($workflow, $current->item);
            if (!$result instanceof NullEvent) {
                $workflow->logs->add($result);
            }
        }

        if ($current->item instanceof Timer) {
            $result = $this->handleTimer($workflow, $current->item);
            if (!$result instanceof NullEvent) {
                $workflow->logs->add($result);
            }
        }

        $nextSteps = $workflow->graph->next($current);

        foreach ($nextSteps as $innerStep) {
            $this->walk($workflow, $innerStep);
        };
    }

    private function runTask(Workflow $workflow, Task $task): Event
    {
        if ($task->status === TaskStatus::COMPLETED || $task->status === TaskStatus::FAILED) {
            return new NullEvent();
        }

        /** @var TaskInterface $taskInstance */
        $taskInstance = $this->locator->get($task->class);

        // check status of task
        async(fn () => $taskInstance->run($task))()
            ->then(fn (mixed $result) => $this->taskCompleted($workflow, $task, $result))
            ->catch(fn (Throwable $error) => $this->taskFailed($workflow, $taskInstance, $task, $error))
            ->finally(fn() => $this->saveWorkflow($workflow));

        // if task is running return task started event
        return new TaskStarted($workflow->id, $task->id);
    }

    private function taskFailed(Workflow $workflow, TaskInterface $taskInstance, Task $task, Throwable $throwable): Task
    {
        $task->status = TaskStatus::PENDING;
        $workflow->logs->add(new TaskErrored($workflow->id, $task->id, $throwable));

        // if max tries is reached task failed
        $task->retries++;
        if ($task->retries >= $taskInstance->maxRetries()) {
            $task->status = TaskStatus::FAILED;
            $workflow->logs->add(new TaskFailed($workflow->id, $task->id));
        }

        return $task;
    }

    private function saveWorkflow(Workflow $workflow): void
    {
        $this->workflowRepository->persist($workflow);
    }

    private function taskCompleted(Workflow $workflow, Task $task, mixed $result): Task
    {
        $task->status = TaskStatus::COMPLETED;
        $workflow->logs->add(new TaskCompleted($workflow->id, $task->id));
        $task->value = resolve($result);

        return $task;
    }

    private function checkSignal(Workflow $workflow, Signal $item): Event
    {
        $active = ($item->predicate)();

        if ($active) {
            return new SignalActivated($workflow->id, $item->name);
        }

        return new SignalQueried($workflow->id, $item->name);
    }

    private function handleTimer(Workflow $workflow, Timer $item): Event
    {
        // Advance workflow time
        $workflow->nextTick = $workflow->nextTick->add(CarbonInterval::seconds($item->seconds));

        return new TimerActivated($workflow->id);
    }
}
