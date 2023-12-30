<?php

declare(strict_types=1);

namespace Lemonade\Workflow\Graph;

use Lemonade\Workflow\DataStorage\Signal;
use Lemonade\Workflow\DataStorage\Task;
use Lemonade\Workflow\DataStorage\Timer;
use Lemonade\Workflow\PayloadInterface;
use Lemonade\Workflow\WorkflowInterface;

/**
 * @phpstan-type DagTypes = Signal|Task|Timer
 */
class DagBuilder
{
    /**
     * @return Dag<DagTypes>
     */
    public function build(WorkflowInterface $workflow, PayloadInterface $payload): Dag
    {
        /** @var Dag<DagTypes> $dag */
        $dag = new Dag([], []);
        $generator = $workflow->execute($payload);
        /** @var DagTypes $item */
        $item = $generator->current();
        $generator->next();
        $lastNode = new Node($item);
        $dag->addNode($lastNode);

        while ($generator->valid()) {
            /** @var DagTypes $item */
            $item = $generator->current();
            $generator->next();
            $currentNode = new Node($item);
            $dag->addNode($currentNode);
            $dag->addEdge($lastNode, $currentNode);
            $lastNode = $currentNode;
        }

        return $dag;
    }
}
