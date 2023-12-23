<?php

declare(strict_types=1);

namespace Lemonade\Workflow\DataStorage;

use Lemonade\Workflow\Enum\WorkflowStatus;
use Lemonade\Workflow\Graph\Dag;
use Ramsey\Uuid\UuidInterface;

class Workflow
{
    public function __construct(
        public readonly UuidInterface $id,
        public readonly string $class,
        public WorkflowStatus $status,
        /** @var Dag<Signal|Task|Timer> $graph */
        public readonly Dag $graph,
    ) {
    }
}
