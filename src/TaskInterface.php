<?php

declare(strict_types=1);

namespace Lemonade\Workflow;

use Lemonade\Workflow\DataStorage\Task;

interface TaskInterface
{
    public function maxRetries(): int;

    /**
     * @return array<int, int>
     */
    public function retryDelay(): array;

    public function run(Task $task): mixed;
}
