<?php

declare(strict_types=1);

namespace Lemonade\Workflow\Graph;

/**
 * @template T
 */
class Node
{
    public function __construct(
        /** @var T $predicate */
        public readonly object $item,
    ) {
    }
}
