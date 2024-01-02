<?php

declare(strict_types=1);

namespace Lemonade\Workflow\DataStorage\Log;

use ArrayIterator;
use IteratorAggregate;
use Lemonade\Workflow\DataStorage\Log\Event\Event;
use Traversable;

/**
 * @implements IteratorAggregate<Event>
 */
class LogCollection implements IteratorAggregate
{
    /**
     * @var Event[]
     */
    private array $logs;

    public function __construct(Event ...$logs)
    {
        $this->logs = $logs;
    }

    public function add(Event $log): void
    {
        $this->logs[] = $log;
    }

    /**
     * @return Event[]
     */
    public function toArray(): array
    {
        return $this->logs;
    }

    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->logs);
    }

    /**
     * @param class-string<Event> $class
     */
    public function filterByEvent(string $class): LogCollection
    {
        return new LogCollection(...array_filter($this->logs, fn (Event $log) => $log instanceof $class));
    }

    public function __toString(): string
    {
        return implode(', ', $this->logs);
    }
}
