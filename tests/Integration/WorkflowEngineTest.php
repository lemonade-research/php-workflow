<?php

declare(strict_types=1);

namespace Lemonade\Workflow\Tests\Integration;

use Lemonade\Workflow\DataStorage\Log\Event\SignalActivated;
use Lemonade\Workflow\DataStorage\Log\Event\TaskErrored;
use Lemonade\Workflow\DataStorage\Log\Event\TaskStarted;
use Lemonade\Workflow\DataStorage\Log\Event\TimerActivated;
use Lemonade\Workflow\DataStorage\Log\LogCollection;
use Lemonade\Workflow\DataStorage\Workflow;
use Lemonade\Workflow\Enum\WorkflowStatus;
use Lemonade\Workflow\Graph\DagBuilder;
use Lemonade\Workflow\Tests\Integration\Fixture\ExamplePayload;
use Lemonade\Workflow\Tests\Integration\Fixture\ExampleWorkflow;
use Lemonade\Workflow\Tests\Integration\Fixture\TaskContainer;
use Lemonade\Workflow\Tests\Integration\Fixture\WorkflowRepository;
use Lemonade\Workflow\WorkflowEngine;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\MessageBusInterface;

class WorkflowEngineTest extends TestCase
{
    use ProphecyTrait;

    private $workflowRepository;

    protected function setUp(): void
    {
        $this->workflowRepository = $this->prophesize(WorkflowRepository::class);
    }

    /**
     * @test
     */
    public function itShouldExecuteTasksCorrectly(): void
    {
        $this->workflowRepository
            ->persist(Argument::type(Workflow::class))
            ->shouldBeCalled();
        $payload = new ExamplePayload();

        $workflow = new ExampleWorkflow();
        $workflowInstance = new Workflow(
            id: Uuid::uuid4(),
            class: $workflow::class,
            status: WorkflowStatus::INITIAL,
            graph: (new DagBuilder())->build($workflow, $payload),
            logs: new LogCollection(),
            payload: $payload,
        );

        $subject = $this->getUnitUnderTest();
        $subject->__invoke($workflowInstance);

        $this->assertCount(1, $workflowInstance->logs->filterByEvent(TaskStarted::class));
    }

    /**
     * @test
     */
    public function itShouldHandleSignalsProperly()
    {
        $this->workflowRepository
            ->persist(Argument::type(Workflow::class))
            ->shouldBeCalled();
        $payload = new ExamplePayload();

        $workflow = new ExampleWorkflow();
        $workflowInstance = new Workflow(
            id: Uuid::uuid4(),
            class: $workflow::class,
            status: WorkflowStatus::INITIAL,
            graph: (new DagBuilder())->build($workflow, $payload),
            logs: new LogCollection(),
            payload: $payload,
        );

        $subject = $this->getUnitUnderTest();
        $subject->__invoke($workflowInstance);
        $subject->__invoke($workflowInstance);
        $subject->__invoke($workflowInstance);
        $subject->__invoke($workflowInstance);

        $this->assertCount(1, $workflowInstance->logs->filterByEvent(SignalActivated::class));
    }

    /**
     * @test
     */
    public function itShouldHandleTimersCorrectly(): void
    {
        $this->workflowRepository
            ->persist(Argument::type(Workflow::class))
            ->shouldBeCalled();
        $payload = new ExamplePayload();

        $workflow = new ExampleWorkflow();
        $workflowInstance = new Workflow(
            id: Uuid::uuid4(),
            class: $workflow::class,
            status: WorkflowStatus::INITIAL,
            graph: (new DagBuilder())->build($workflow, $payload),
            logs: new LogCollection(),
            payload: $payload,
        );

        $subject = $this->getUnitUnderTest();
        $subject->__invoke($workflowInstance);
        $subject->__invoke($workflowInstance);

        $this->assertCount(1, $workflowInstance->logs->filterByEvent(TimerActivated::class));
    }

    /**
     * @test
     */
    public function itShouldHandleErrorsProperly(): void
    {
        $payload = new ExamplePayload(true);

        $workflow = new ExampleWorkflow();
        $workflowInstance = new Workflow(
            id: Uuid::uuid4(),
            class: $workflow::class,
            status: WorkflowStatus::INITIAL,
            graph: (new DagBuilder())->build($workflow, $payload),
            logs: new LogCollection(),
            payload: $payload,
        );

        $subject = $this->getUnitUnderTest();
        $subject->__invoke($workflowInstance);
        $subject->__invoke($workflowInstance);
        $subject->__invoke($workflowInstance);

        $this->assertCount(1, $workflowInstance->logs->filterByEvent(TaskErrored::class));
    }

    /**
     * @test
     */
    public function itShouldBeSerializable(): void
    {
        $payload = new ExamplePayload(true);

        $workflow = new ExampleWorkflow();
        $workflowInstance = new Workflow(
            id: Uuid::uuid4(),
            class: $workflow::class,
            status: WorkflowStatus::INITIAL,
            graph: (new DagBuilder())->build($workflow, $payload),
            logs: new LogCollection(),
            payload: $payload,
        );

        $subject = $this->getUnitUnderTest();
        $subject->__invoke($workflowInstance);

        $this->assertIsString(serialize($workflowInstance));
    }

    public function itShouldHandleTimeoutsProperly(): void
    {
        // Test the handling of timeouts
    }

    public function itShouldCompleteOverallWorkflowSuccessfully(): void
    {
        // Test a complete workflow execution
    }

    private function getUnitUnderTest(): WorkflowEngine
    {
        $messageBus = $this->prophesize(MessageBusInterface::class);
        $messageBus->dispatch(Argument::cetera())->willReturn(new Envelope(new \stdClass()));

        return new WorkflowEngine(
            new TaskContainer(),
            $this->workflowRepository->reveal(),
            $messageBus->reveal(),
        );
    }
}
