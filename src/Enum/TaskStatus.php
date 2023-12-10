<?php

declare(strict_types=1);

namespace Lemonade\Workflow\Enum;

enum TaskStatus
{
    case INITIAL;
    case COMPLETED;
    case FAILED;
}
