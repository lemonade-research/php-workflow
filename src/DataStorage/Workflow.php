<?php

declare(strict_types=1);

namespace Lemonade\Workflow\DataStorage;

use Carbon\CarbonInterval;
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
    private const DEFAULT_TICK_INTERVAL = 10;

    public readonly \DateTimeImmutable $createdAt;
    public \DateTimeImmutable $nextTick;

    public function __construct(
        public readonly UuidInterface $id,
        public readonly string $class,
        public WorkflowStatus $status,
        /** @var Dag<DagTypes> $graph */
        public readonly Dag $graph,
        public readonly LogCollection $logs,
    ) {
        $this->createdAt = new \DateTimeImmutable();
        $this->nextTick = $this->createdAt->add(CarbonInterval::seconds(self::DEFAULT_TICK_INTERVAL));
    }
}
