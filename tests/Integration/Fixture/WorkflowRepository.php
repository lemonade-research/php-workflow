<?php

declare(strict_types=1);

namespace Lemonade\Workflow\Tests\Integration\Fixture;

use Lemonade\Workflow\DataStorage\Workflow;
use Lemonade\Workflow\DataStorage\WorkflowRepositoryInterface;
use Ramsey\Uuid\UuidInterface;

class WorkflowRepository implements WorkflowRepositoryInterface
{
    /** @var array<string, Workflow> */
    private array $storage = [];

    public function get(string $class, UuidInterface $id): Workflow
    {
        if (!isset($this->storage[$id->toString()])) {
            throw new \LogicException(sprintf('Workflow %s with id %s does not exist', $class, $id->toString()));
        }
        return $this->storage[$id->toString()];
    }

    public function persist(Workflow $workflow): void
    {
        $this->storage[$workflow->id->toString()] = $workflow;
    }
}
