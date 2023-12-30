<?php

declare(strict_types=1);

namespace Lemonade\Workflow;

use Lemonade\Workflow\DataStorage\Log\LogCollection;
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
use React\Promise\Deferred;
use Symfony\Component\Messenger\MessageBusInterface;

use function React\Promise\resolve;

/**
 * Is responsible for managing workflows.
 *  - It creates a workflow based on a given workflow instance and starts the run immediately.
 *  - It loads a workflow from the data storage.
 *  - It resumes a workflow.
 */
class WorkflowManager
{
    public function __construct(
        private readonly WorkflowRepositoryInterface $workflowRepository,
        private readonly DagBuilder $dagBuilder,
        private readonly MessageBusInterface $messageBus,
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
            WorkflowStatus::INITIAL,
            $this->dagBuilder->build($workflowInstance),
            new LogCollection(),
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
        $this->execute($workflow);
    }

    /**
     * @param class-string<TaskInterface> $task
     */
    public static function run(string $task): Task
    {
        return new Task(
            Uuid::uuid4(),
            Uuid::uuid4(),
            $task,
            TaskStatus::INITIAL,
            (new Deferred())->promise(),
        );
    }

    public static function timer(int $seconds): Timer
    {
        return new Timer($seconds);
    }

    public static function await(string $name, \Closure $predicate): Signal
    {
        return new Signal($name, $predicate);
    }

    private function execute(Workflow $workflow): void
    {
        $this->messageBus->dispatch($workflow);
    }
}
