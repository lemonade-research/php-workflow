<?php

declare(strict_types=1);

namespace Lemonade\Workflow\DataStorage\Log\Event;

class NullEvent implements Event
{
    public function __toString(): string
    {
        return sprintf('%s()', self::class);
    }
}
