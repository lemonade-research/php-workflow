<?php

declare(strict_types=1);

namespace Lemonade\Workflow\DataStorage;

readonly class Signal
{
    public function __construct(
        public string $name,
        public bool $predicateResult,
    ) {
    }
}
