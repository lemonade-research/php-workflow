<?php

declare(strict_types=1);

namespace Lemonade\Workflow\DataStorage;

use React\Promise\PromiseInterface;

class Signal
{
    public function __construct(
        public readonly string $name,
        /** @var \Closure $predicate */
        public readonly \Closure $predicate
    ) {
    }
}
