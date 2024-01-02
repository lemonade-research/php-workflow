<?php

declare(strict_types=1);

namespace Lemonade\Workflow\Enum;

enum WorkflowStatus : string
{
    case INITIAL = 'initial';
    case RUNNING = 'running';
    case PENDING = 'pending';
    case COMPLETED = 'completed';
    case FAILED = 'failed';
}
