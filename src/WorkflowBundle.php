<?php

declare(strict_types=1);

namespace Lemonade\Workflow;

use Symfony\Component\Console\Application;
use Symfony\Component\HttpKernel\Bundle\AbstractBundle;

class WorkflowBundle extends AbstractBundle
{
    /** @return void */
    public function registerCommands(Application $application)
    {
    }
}
