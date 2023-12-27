<?php

declare(strict_types=1);

namespace Lemonade\Workflow\Enum;

enum WorkflowStatus
{
    case INITIAL;
    case RUNNING;
    case PENDING;
    case COMPLETED;
    case FAILED;
}
