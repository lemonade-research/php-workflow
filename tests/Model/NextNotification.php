<?php

declare(strict_types=1);

namespace Lemonade\Workflow\Tests\Model;

final class NextNotification extends Notification
{
    public function __construct(int $frame, public readonly string $identifier)
    {
        $this->frame = $frame;
    }
}
