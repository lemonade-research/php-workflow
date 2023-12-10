<?php

declare(strict_types=1);

namespace Lemonade\Workflow\DataStorage;

class Timer
{
    public function __construct(public readonly int $seconds)
    {
    }
}
