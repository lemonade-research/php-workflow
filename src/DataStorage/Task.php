<?php

declare(strict_types=1);

namespace Lemonade\Workflow\DataStorage;

use Lemonade\Workflow\Enum\TaskStatus;
use Ramsey\Uuid\UuidInterface;
use React\Promise\PromiseInterface;

class Task
{
    public function __construct(
        public readonly UuidInterface $id,
        public readonly UuidInterface $workflowId,
        public readonly string $class,
        public readonly TaskStatus $status,
        /** @var PromiseInterface<mixed> $value */
        public readonly PromiseInterface $value,
    ) {
    }
}