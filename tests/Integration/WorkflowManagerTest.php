<?php

declare(strict_types=1);

namespace Lemonade\Workflow\Tests\Integration;

use Lemonade\Workflow\Enum\WorkflowStatus;
use Lemonade\Workflow\Graph\DagBuilder;
use Lemonade\Workflow\Tests\Integration\Fixture\ExamplePayload;
use Lemonade\Workflow\Tests\Integration\Fixture\ExampleWorkflow;
use Lemonade\Workflow\Tests\Integration\Fixture\WorkflowRepository;
use Lemonade\Workflow\WorkflowManager;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Messenger\MessageBus;

class WorkflowManagerTest extends TestCase
{
    /**
     * @test
     */
    public function itShouldStartAWorkflow(): void
    {
        $manager = $this->getUnitUnderTest();
        $workflow = $manager->start(ExampleWorkflow::class, new ExamplePayload());

        $this->assertSame(WorkflowStatus::INITIAL, $workflow->status);
    }

    /**
     * @test
     */
    public function itShouldRaiseExceptionIfGivenWorkflowNotImplementsWorkflowInterface(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        $manager = $this->getUnitUnderTest();
        $manager->start(self::class);
    }

    /**
     * @test
     */
    public function itShouldLoadAWorkflow(): void
    {
        $manager = $this->getUnitUnderTest();
        $workflow = $manager->start(ExampleWorkflow::class, new ExamplePayload());
        $loadedWorkflow = $manager->load($workflow->id);

        $this->assertSame($workflow->id, $loadedWorkflow->id);
        $this->assertSame($workflow->class, $loadedWorkflow->class);
        $this->assertSame($workflow->status, $loadedWorkflow->status);
    }

    /**
     * @test
     */
    public function itShouldPutWorkflowInRunningStateOnResume(): void
    {
        $manager = $this->getUnitUnderTest();
        $workflow = $manager->start(ExampleWorkflow::class, new ExamplePayload());
        $loadedWorkflow = $manager->load($workflow->id);
        $manager->resume($loadedWorkflow);

        $this->assertSame(WorkflowStatus::INITIAL, $loadedWorkflow->status);
    }

    private function getUnitUnderTest(): WorkflowManager
    {
        return new WorkflowManager(
            new WorkflowRepository(),
            new DagBuilder(),
            new MessageBus(),
        );
    }
}
