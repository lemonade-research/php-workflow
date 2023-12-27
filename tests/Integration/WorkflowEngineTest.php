<?php

declare(strict_types=1);

namespace Lemonade\Workflow\Tests\Integration;

use Lemonade\Workflow\Tests\Integration\Fixture\TaskContainer;
use Lemonade\Workflow\DataStorage\Log\Event\TaskStarted;
use Lemonade\Workflow\DataStorage\Log\LogCollection;
use Lemonade\Workflow\DataStorage\Workflow;
use Lemonade\Workflow\Enum\WorkflowStatus;
use Lemonade\Workflow\Graph\DagBuilder;
use Lemonade\Workflow\Tests\Integration\Fixture\ExampleWorkflow;
use Lemonade\Workflow\Tests\Integration\Fixture\WorkflowRepository;
use Lemonade\Workflow\WorkflowEngine;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;
use Symfony\Component\DependencyInjection\Container;

class WorkflowEngineTest extends TestCase
{
    /**
     * @test
     */
    public function itShouldExecuteTasksCorrectly()
    {
        $workflow = new ExampleWorkflow();
        $workflowInstance = new Workflow(
            id: Uuid::uuid4(),
            class: $workflow::class,
            status: WorkflowStatus::INITIAL,
            graph: (new DagBuilder())->build($workflow),
            logs: new LogCollection(),
        );

        $subject = $this->getUnitUnderTest();
        $subject->__invoke($workflowInstance);

        $this->assertCount(2, $workflowInstance->logs->filterByEvent(TaskStarted::class));
    }

    public function itShouldPersistStateCorrectly()
    {
        // Test if the workflow state is persisted correctly
    }

    public function itShouldHandleSignalsProperly()
    {
        // Test handling of different signals
    }

    public function itShouldHandleTimersCorrectly()
    {
        // Test the timer functionality
    }

    public function itShouldHandleErrorsProperly()
    {
        // Test error handling during task execution
    }

    public function itShouldImplementRetryMechanismCorrectly()
    {
        // Test the retry logic
    }

    public function itShouldHandleTimeoutsProperly()
    {
        // Test the handling of timeouts
    }

    public function itShouldCompleteOverallWorkflowSuccessfully()
    {
        // Test a complete workflow execution
    }

    public function itShouldHandleConcurrencyAndParallelismEffectively()
    {
        // Test handling of concurrent tasks and parallel paths
    }

    public function itShouldPerformWellUnderDifferentLoads()
    {
        // Test performance under different loads
    }

    private function getUnitUnderTest(): WorkflowEngine
    {
        return new WorkflowEngine(
            new TaskContainer(),
            new WorkflowRepository(),
        );
    }
}
