<?php

declare(strict_types=1);

namespace Lemonade\Workflow\DataStorage\Log\Event;

use Carbon\Carbon;
use Ramsey\Uuid\UuidInterface;
use React\Promise\PromiseInterface;

class TaskStarted implements Event
{
    public readonly Carbon $createdAt;

    public function __construct(
        public readonly UuidInterface $workflowId,
        public readonly UuidInterface $taskId,
    ) {
        $this->createdAt = Carbon::now();
    }
}
