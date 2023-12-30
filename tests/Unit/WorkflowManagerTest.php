<?php

declare(strict_types=1);

namespace Lemonade\Workflow\Tests\Unit;

use Lemonade\Workflow\DataStorage\WorkflowRepositoryInterface;
use Lemonade\Workflow\Enum\TaskStatus;
use Lemonade\Workflow\Graph\DagBuilder;
use Lemonade\Workflow\TaskInterface;
use Lemonade\Workflow\Tests\AssertsPromiseTrait;
use Lemonade\Workflow\WorkflowEngine;
use Lemonade\Workflow\WorkflowManager;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Symfony\Component\Messenger\MessageBusInterface;

class WorkflowManagerTest extends TestCase
{
    use ProphecyTrait;
    use AssertsPromiseTrait;

    private $repository;
    private $dagBuilder;
    private $messageBus;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = $this->prophesize(WorkflowRepositoryInterface::class);
        $this->dagBuilder = $this->prophesize(DagBuilder::class);
        $this->messageBus = $this->prophesize(MessageBusInterface::class);
    }

    /**
     * @test
     */
    public function itShouldReturnTaskOnFactoryMethodRun(): void
    {
        $task = WorkflowManager::run(TaskInterface::class);

        $this->assertSame(TaskInterface::class, $task->class);
        $this->assertSame(TaskStatus::INITIAL, $task->status);
    }

    /**
     * @test
     */
    public function itShouldReturnTimerOnFactoryMethodTimer(): void
    {
        $timer = WorkflowManager::timer(123);

        $this->assertSame(123, $timer->seconds);
    }


    /**
     * @test
     */
    public function itShouldReturnSignalOnFactoryMethodAwait(): void
    {
        $signal = WorkflowManager::await('foo', fn() => 123 > 12);

        $this->assertTrue($signal->predicateResult);
    }

    /**
     * @test
     */
    public function itShouldRaiseExceptionIfGivenWorkflowClassDoesNotExist(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        $manager = $this->getUnitUnderTest();
        $manager->start('Foo\Bar\Baz');
    }

    private function getUnitUnderTest(): WorkflowManager
    {
        return new WorkflowManager(
            $this->repository->reveal(),
            $this->dagBuilder->reveal(),
            $this->messageBus->reveal(),
        );
    }
}
