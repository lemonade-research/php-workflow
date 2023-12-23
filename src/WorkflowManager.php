<?php

declare(strict_types=1);

namespace Lemonade\Workflow;

use Lemonade\Workflow\DataStorage\Signal;
use Lemonade\Workflow\DataStorage\Task;
use Lemonade\Workflow\DataStorage\Timer;
use Lemonade\Workflow\DataStorage\Workflow;
use Lemonade\Workflow\DataStorage\WorkflowRepositoryInterface;
use Lemonade\Workflow\Enum\TaskStatus;
use Lemonade\Workflow\Enum\WorkflowStatus;
use Lemonade\Workflow\Graph\DagBuilder;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

use function React\Promise\resolve;

class WorkflowManager
{
    public function __construct(
        private readonly WorkflowRepositoryInterface $workflowRepository,
        private readonly DagBuilder $dagBuilder,
        private readonly WorkflowEngine $workflowEngine,
    ) {
    }

    /**
     * Creates a workflow based on a given workflow instance and starts the run immediately.
     *
     * @param class-string<WorkflowInterface> $class
     */
    public function start(string $class): Workflow
    {
        if (!class_exists($class)) {
            throw new \InvalidArgumentException(sprintf('Class %s does not exist', $class));
        }
        if (!in_array(WorkflowInterface::class, class_implements($class))) {
            throw new \InvalidArgumentException(sprintf('Class %s does not implement Workflow interface', $class));
        }

        $workflowInstance = new $class();

        $workflow = new Workflow(
            Uuid::uuid4(),
            $class,
            WorkflowStatus::RUNNING,
            $this->dagBuilder->build($workflowInstance),
        );

        $this->workflowRepository->persist($workflow);

        $this->execute($workflow);

        return $workflow;
    }

    public function load(string $class, UuidInterface $id): Workflow
    {
        return $this->workflowRepository->get($class, $id);
    }

    public function resume(Workflow $workflow): void
    {
        $workflow->status = WorkflowStatus::RUNNING;
        // run workflow
        $this->execute($workflow);
    }

    public static function run(TaskInterface $task): Task
    {
        return new Task(
            Uuid::uuid4(),
            Uuid::uuid4(),
            $task::class,
            TaskStatus::INITIAL,
            resolve(true),
        );
    }

    public static function timer(int $seconds): Timer
    {
        return new Timer($seconds);
    }

    public static function await(callable $callable): Signal
    {
        return new Signal(resolve($callable()));
    }

    private function execute(Workflow $workflow): void
    {
        $this->workflowEngine->run($workflow);
    }
}
