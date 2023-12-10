<?php

declare(strict_types=1);

namespace Lemonade\Workflow\Tests\Integration\Fixture;

use Lemonade\Workflow\WorkflowInterface;

class ExampleWorkflow implements WorkflowInterface
{
    public function execute(): \Generator
    {
        yield 'foo';
        yield 'bar';
        yield 'baz';
    }
}
