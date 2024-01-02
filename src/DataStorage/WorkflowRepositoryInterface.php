<?php

declare(strict_types=1);

namespace Lemonade\Workflow\DataStorage;

use Lemonade\Workflow\Enum\WorkflowStatus;
use Ramsey\Uuid\UuidInterface;

interface WorkflowRepositoryInterface
{
    public function get(UuidInterface $id): Workflow;
    public function persist(Workflow $workflow): void;

    /**
     * @return \Generator<Workflow>
     */
    public function nextWorkflows(): \Generator;

    /**
     * @return Workflow[]
     */
    public function findByStatus(WorkflowStatus $status): array;
}
