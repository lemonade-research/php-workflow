<?php

declare(strict_types=1);

namespace Lemonade\Workflow\Tests\Integration\Fixture;

use Psr\Container\ContainerInterface;

class TaskContainer implements ContainerInterface
{
    public function get(string $id)
    {
        return match ($id) {
            ExampleTask::class => new ExampleTask(),
            default => throw new \LogicException(sprintf('Task %s does not exist', $id)),
        };
    }

    public function has(string $id): bool
    {
        // TODO: Implement has() method.
    }
}
