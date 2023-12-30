<?php

declare(strict_types=1);

namespace Lemonade\Workflow\DataStorage\Log\Event;

use Carbon\Carbon;
use Ramsey\Uuid\UuidInterface;

class TimerActivated implements Event
{
    public readonly Carbon $createdAt;

    public function __construct(
        public readonly UuidInterface $workflowId,
    ) {
        $this->createdAt = Carbon::now();
    }
}
