<?php

declare(strict_types=1);

namespace Lemonade\Workflow\Tests\Integration\Fixture;

use Lemonade\Workflow\DataStorage\Workflow;
use Lemonade\Workflow\DataStorage\WorkflowRepositoryInterface;
use Lemonade\Workflow\Enum\WorkflowStatus;
use Ramsey\Uuid\UuidInterface;

class WorkflowRepository implements WorkflowRepositoryInterface
{
    /** @var array<string, Workflow> */
    private array $storage = [];

    public function get(UuidInterface $id): Workflow
    {
        if (!isset($this->storage[$id->toString()])) {
            throw new \LogicException(sprintf('Workflow with id %s does not exist', $id->toString()));
        }
        return $this->storage[$id->toString()];
    }

    public function persist(Workflow $workflow): void
    {
        $this->storage[$workflow->id->toString()] = $workflow;
    }

    public function nextWorkflows(): \Generator
    {
        foreach ($this->storage as $workflow) {
            if ($workflow->status == WorkflowStatus::PENDING) {
                yield $workflow;
            }
        }
    }

    public function findByStatus(WorkflowStatus $status): array
    {
        return array_values(array_filter($this->storage, fn(Workflow $workflow) => $workflow->status === $status));
    }
}
