<?php

declare(strict_types=1);

namespace Lemonade\Workflow;

/**
 *
 */
interface WorkflowInterface
{
    public function execute(PayloadInterface $payload): \Generator;
}
