<?php

declare(strict_types=1);

namespace Lemonade\Workflow\Tests\Integration\Fixture;

use Lemonade\Workflow\WorkflowInterface;
use Lemonade\Workflow\WorkflowManager;

class ExampleWorkflow implements WorkflowInterface
{
    public function execute(): \Generator
    {
        yield WorkflowManager::run(new ExampleTask());
        yield WorkflowManager::timer(10);
        yield WorkflowManager::run(new ExampleTask());
    }
}
