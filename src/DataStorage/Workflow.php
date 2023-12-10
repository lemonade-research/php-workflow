<?php

declare(strict_types=1);

namespace Lemonade\Workflow\DataStorage;

use Lemonade\Workflow\Enum\WorkflowStatus;
use Ramsey\Uuid\UuidInterface;

class Workflow
{
    public function __construct(
        public readonly UuidInterface $id,
        public readonly string $class,
        public WorkflowStatus $status,
    ) {
    }
}
