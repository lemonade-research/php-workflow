<?php

declare(strict_types=1);

namespace Lemonade\Workflow\Tests\Integration\Fixture;

use Lemonade\Workflow\PayloadInterface;
use Lemonade\Workflow\WorkflowInterface;
use Lemonade\Workflow\WorkflowManager;

class ExampleWorkflow implements WorkflowInterface
{
    /**
     * @param ExamplePayload $payload
     */
    public function execute(PayloadInterface $payload): \Generator
    {
        yield WorkflowManager::run(ExampleTask::class);
        yield WorkflowManager::timer(10);
        yield WorkflowManager::run(ExampleTask::class, ['error' => $payload->error]);
        yield WorkflowManager::await('signal', fn() => true);
        yield WorkflowManager::run(ExampleTask::class);
    }
}
