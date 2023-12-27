<?php

declare(strict_types=1);

namespace Lemonade\Workflow\DataStorage;

use Lemonade\Workflow\DataStorage\Log\LogCollection;
use Lemonade\Workflow\Enum\WorkflowStatus;
use Lemonade\Workflow\Graph\Dag;
use Lemonade\Workflow\Graph\DagBuilder;
use Ramsey\Uuid\UuidInterface;

/**
 * @phpstan-import-type DagTypes from DagBuilder
 */
class Workflow
{
    public function __construct(
        public readonly UuidInterface $id,
        public readonly string $class,
        public WorkflowStatus $status,
        /** @var Dag<DagTypes> $graph */
        public readonly Dag $graph,
        public readonly LogCollection $logs,
    ) {
    }
}
