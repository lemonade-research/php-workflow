<?php

declare(strict_types=1);

namespace Lemonade\Workflow\Graph;

/**
 * @template T of object
 */
class Node
{
    public function __construct(
        /** @var T $item */
        public readonly object $item,
    ) {
    }
}
