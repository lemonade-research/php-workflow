<?php

declare(strict_types=1);

namespace Lemonade\Workflow\Enum;

enum TaskStatus : string
{
    case INITIAL = 'initial';
    case COMPLETED = 'completed';
    case FAILED = 'failed';
    case PENDING = 'pending';
}
