<?php

declare(strict_types=1);

namespace Lemonade\Workflow\DataStorage;

use Ramsey\Uuid\UuidInterface;

interface WorkflowRepositoryInterface
{
    public function get(string $class, UuidInterface $id): Workflow;
    public function persist(Workflow $workflow): void;
}
