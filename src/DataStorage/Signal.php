<?php

declare(strict_types=1);

namespace Lemonade\Workflow\DataStorage;

use React\Promise\PromiseInterface;

class Signal
{
    public function __construct(
        /** @var PromiseInterface<bool> $predicate */
        public readonly PromiseInterface $predicate
    ) {
    }
}
