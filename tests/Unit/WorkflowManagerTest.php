<?php

declare(strict_types=1);

namespace Lemonade\Workflow\Tests\Unit;

use Lemonade\Workflow\DataStorage\WorkflowRepositoryInterface;
use Lemonade\Workflow\Enum\TaskStatus;
use Lemonade\Workflow\TaskInterface;
use Lemonade\Workflow\Tests\AssertsPromiseTrait;
use Lemonade\Workflow\WorkflowManager;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;

class WorkflowManagerTest extends TestCase
{
    use ProphecyTrait;
    use AssertsPromiseTrait;

    private $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = $this->prophesize(WorkflowRepositoryInterface::class);
    }

    /**
     * @test
     */
    public function itShouldReturnTaskOnFactoryMethodRun(): void
    {
        $taskInstance = $this->prophesize(TaskInterface::class)->reveal();
        $task = WorkflowManager::run($taskInstance);

        $this->assertSame($taskInstance::class, $task->class);
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
        $signal = WorkflowManager::await(fn() => 123 > 12);

        $this->assertPromiseFulfillsWith($signal->predicate, true);
    }


    private function getUnitUnderTest(): WorkflowManager
    {
        return new WorkflowManager($this->repository->reveal());
    }
}
