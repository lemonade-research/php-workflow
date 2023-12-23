<?php

declare(strict_types=1);

namespace Lemonade\Workflow;

use Lemonade\Workflow\DataStorage\Workflow;
use Lemonade\Workflow\Enum\WorkflowStatus;

/**
 * Is responsible for running a workflow.
 *  - It iterates over the graph and executes tasks
 *  - It persists the workflow state
 *  - It handles signals
 *  - It handles timers
 *  - It handles errors
 *  - It handles retries
 *  - It handles timeouts
 *
 * @internal
 */
class WorkflowEngine
{
    public function run(Workflow $workflow): void
    {
        // iterate over graph and execute tasks
    }
}
