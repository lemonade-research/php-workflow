<?php

declare(strict_types=1);

namespace Lemonade\Workflow;

use Lemonade\Workflow\DataStorage\Task;
use Lemonade\Workflow\DataStorage\Workflow;

interface TaskInterface
{
    public function maxRetries(): int;

    /**
     * @return array<int, int>
     */
    public function retryDelay(): array;

    public function run(Workflow $workflow, Task $task): mixed;
}
