<?php

declare(strict_types=1);

namespace Lemonade\Workflow\DataStorage;

use Lemonade\Workflow\Enum\TaskStatus;
use Ramsey\Uuid\UuidInterface;
use React\Promise\PromiseInterface;

use function React\Promise\resolve;

class Task
{
    public function __construct(
        public readonly UuidInterface $id,
        public readonly UuidInterface $workflowId,
        public readonly string $class,
        public TaskStatus $status,
        /** @var PromiseInterface<mixed> $value */
        public PromiseInterface $value,
        public int $retries = 0,
        /** @var array<string, mixed> $parameters */
        public array $parameters = [],
    ) {
    }
}
