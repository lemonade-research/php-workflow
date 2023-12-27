<?php

declare(strict_types=1);

namespace Lemonade\Workflow;

use Lemonade\Workflow\DataStorage\Task;

interface TaskInterface
{
    public function maxRetries(): int;
    public function run(Task $task): mixed;
}
