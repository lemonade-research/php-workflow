<?php

declare(strict_types=1);

namespace Lemonade\Workflow\Tests\Integration\Fixture;

use Lemonade\Workflow\DataStorage\Task;
use Lemonade\Workflow\TaskInterface;

class ExampleTask implements TaskInterface
{
    public function maxRetries(): int
    {
        return 3;
    }

    public function run(Task $task): string
    {
        if ($task->parameters['error'] ?? false) {
            throw new \Exception('Error');
        }

        return sprintf('Hello %s!', $task->id);
    }

    public function retryDelay(): array
    {
        return [1, 5, 10];
    }
}
