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
use Lemonade\Workflow\Enum\WorkflowStatus;
use Lemonade\Workflow\Graph\DagBuilder;
use Lemonade\Workflow\Graph\Node;
use Psr\Container\ContainerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\DelayStamp;
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
        private readonly MessageBusInterface $messageBus,
    ) {
    }

    public function __invoke(Workflow $workflow): void
    {
        // iterate over graph and execute tasks
        $current = $workflow->graph->current();
        $workflow->status = WorkflowStatus::RUNNING;

        $this->walk($workflow, $current);

        $this->saveWorkflow($workflow);
    }

    /**
     * @param Node<DagTypes> $current
     */
    private function walk(Workflow $workflow, Node $current): void
    {
        if ($current->item instanceof Task) {
            $this->runTask($workflow, $current->item);
        }

        if ($current->item instanceof Signal) {
            $this->checkSignal($workflow, $current->item);
        }

        if ($current->item instanceof Timer) {
            $this->handleTimer($workflow, $current->item);
        }

        if ($workflow->status === WorkflowStatus::FAILED) {
            return;
        }

        $nextSteps = $workflow->graph->next($current);

        foreach ($nextSteps as $next) {
            $workflow->graph->nextIndex($next);
        }

        $this->saveWorkflow($workflow);

        $this->messageBus->dispatch($workflow, [DelayStamp::delayUntil($workflow->nextTick)]);
    }

    private function runTask(Workflow $workflow, Task $task): void
    {
        if ($task->status === TaskStatus::COMPLETED || $task->status === TaskStatus::FAILED) {
            return ;
        }

        $workflow->logs->add(new TaskStarted($workflow->id, $task->id, $task->class));

        /** @var TaskInterface $taskInstance */
        $taskInstance = $this->locator->get($task->class);

        // check status of task
        try {
            $task->status = TaskStatus::PENDING;
            $result = $taskInstance->run($workflow, $task);
            $this->taskCompleted($workflow, $task, $result);
        } catch (Throwable $error) {
            $this->taskFailed($workflow, $taskInstance, $task, $error);
        } finally {
            $this->saveWorkflow($workflow);
        }
    }

    private function taskFailed(Workflow $workflow, TaskInterface $taskInstance, Task $task, Throwable $throwable): Task
    {
        $delay = $taskInstance->retryDelay()[$task->retries] ?? 10;
        $task->status = TaskStatus::PENDING;
        $workflow->logs->add(new TaskErrored($workflow->id, $task->id, $throwable->__toString()));
        $workflow->nextTick = $workflow->nextTick->add(CarbonInterval::seconds($delay));

        // if max tries is reached task failed
        $task->retries++;
        if ($task->retries >= $taskInstance->maxRetries()) {
            $workflow->status = WorkflowStatus::FAILED;
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
        $workflow->status = WorkflowStatus::PENDING;
        $task->status = TaskStatus::COMPLETED;
        $workflow->logs->add(new TaskCompleted($workflow->id, $task->id, $task->class));
        $task->value = $result;

        return $task;
    }

    private function checkSignal(Workflow $workflow, Signal $item): void
    {
        if ($item->predicateResult) {
            $workflow->logs->add(new SignalActivated($workflow->id, $item->name));
            return;
        }

        $workflow->logs->add(new SignalQueried($workflow->id, $item->name));
        $workflow->nextTick = $workflow->nextTick->add(CarbonInterval::seconds(10));
    }

    private function handleTimer(Workflow $workflow, Timer $item): void
    {
        // Advance workflow time
        $workflow->nextTick = $workflow->nextTick->add(CarbonInterval::seconds($item->seconds));
        $workflow->logs->add(new TimerActivated($workflow->id));
    }
}
