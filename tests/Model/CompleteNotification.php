<?php

declare(strict_types=1);

namespace Lemonade\Workflow\Tests\Model;

final class CompleteNotification extends Notification
{
    public function __construct(int $frame)
    {
        $this->frame = $frame;
    }
}
