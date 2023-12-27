<?php

declare(strict_types=1);

namespace Lemonade\Workflow\Tests\Unit\DataStorage\Log;

use ArrayIterator;
use Lemonade\Workflow\DataStorage\Log\Event\TaskFailed;
use Lemonade\Workflow\DataStorage\Log\Event\TaskStarted;
use Lemonade\Workflow\DataStorage\Log\LogCollection;
use PHPUnit\Framework\TestCase;
use Lemonade\Workflow\DataStorage\Log\Event\Event;
use Prophecy\PhpUnit\ProphecyTrait;

class LogCollectionTest extends TestCase
{
    use ProphecyTrait;

    /**
     * @test
     */
    public function itShouldReturnAllLogs()
    {
        $log1 = $this->prophesize(Event::class)->reveal();
        $log2 = $this->prophesize(Event::class)->reveal();

        $logCollection = new LogCollection($log1, $log2);

        $this->assertEquals([$log1, $log2], $logCollection->toArray());
    }

    /**
     * @test
     */
    public function itShouldProvideIterator()
    {
        $log1 = $this->prophesize(Event::class)->reveal();
        $log2 = $this->prophesize(Event::class)->reveal();

        $logCollection = new LogCollection($log1, $log2);
        $iterator = $logCollection->getIterator();

        $this->assertInstanceOf(ArrayIterator::class, $iterator);
        $this->assertCount(2, $iterator);
    }

    /**
     * @test
     */
    public function itShouldFilterByEventType(): void
    {
        $log1 = $this->prophesize(TaskStarted::class)->reveal();
        $log2 = $this->prophesize(TaskFailed::class)->reveal();
        $log3 = $this->prophesize(TaskStarted::class)->reveal();

        $logCollection = new LogCollection();
        $logCollection->add($log1);
        $logCollection->add($log2);
        $logCollection->add($log3);
        $actual = $logCollection->filterByEvent(TaskFailed::class);

        $this->assertCount(1, $actual->getIterator());
        $this->assertEquals([$log2], iterator_to_array($actual->getIterator()));
    }
}
