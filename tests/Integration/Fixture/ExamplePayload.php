<?php

declare(strict_types=1);

namespace Lemonade\Workflow\Tests\Integration\Fixture;

use Lemonade\Workflow\PayloadInterface;

class ExamplePayload implements PayloadInterface
{
    public function __construct(
        public readonly bool $error = false,
    ) {
    }
}
