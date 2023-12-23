<?php

declare(strict_types=1);

namespace Lemonade\Workflow\Tests\Model;

abstract class Notification
{
    protected int $frame;

    public static function next(int $frame, string $identifier): NextNotification
    {
        return new NextNotification($frame, $identifier);
    }

    public static function complete(int $frame): CompleteNotification
    {
        return new CompleteNotification($frame);
    }

    public static function error(int $frame, string $error): ErrorNotification
    {
        return new ErrorNotification($frame, $error);
    }

    public function getFrame(): int
    {
        return $this->frame;
    }
}
