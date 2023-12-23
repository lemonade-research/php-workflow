<?php

declare(strict_types=1);

namespace Lemonade\Workflow\Tests\Model;

final class ErrorNotification extends Notification
{
    public function __construct(int $frame, public readonly string $error)
    {
        $this->frame = $frame;
    }
}
