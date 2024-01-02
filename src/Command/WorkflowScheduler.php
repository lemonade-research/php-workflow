<?php

declare(strict_types=1);

namespace Lemonade\Workflow\Command;

use Lemonade\Workflow\DataStorage\WorkflowRepositoryInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Messenger\MessageBusInterface;

#[AsCommand(
    name: 'workflow:scheduler',
    description: 'Scheduling workflows',
)]
class WorkflowScheduler extends Command
{
    public function __construct(
        private readonly MessageBusInterface $messageBus,
        private readonly WorkflowRepositoryInterface $workflowRepository
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $workflows = $this->workflowRepository->nextWorkflows();

        foreach ($workflows as $workflow) {
            $this->messageBus->dispatch($workflow);
        }

        return Command::SUCCESS;
    }
}
