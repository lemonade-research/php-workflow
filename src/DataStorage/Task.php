<?php

declare(strict_types=1);

namespace Lemonade\Workflow\DataStorage;

use Lemonade\Workflow\Enum\TaskStatus;
use Ramsey\Uuid\UuidInterface;

class Task
{
    public function __construct(
        public readonly UuidInterface $id,
        public readonly string $class,
        public TaskStatus $status,
        public mixed $value,
        public int $retries = 0,
        /** @var array<string, mixed> $parameters */
        public array $parameters = [],
    ) {
    }
}
