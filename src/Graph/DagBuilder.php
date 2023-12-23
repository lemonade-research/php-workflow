<?php

declare(strict_types=1);

namespace Lemonade\Workflow\Graph;

use Lemonade\Workflow\DataStorage\Signal;
use Lemonade\Workflow\DataStorage\Task;
use Lemonade\Workflow\DataStorage\Timer;
use Lemonade\Workflow\WorkflowInterface;

class DagBuilder
{
    /**
     * @return Dag<Signal|Task|Timer>
     */
    public function build(WorkflowInterface $workflow): Dag
    {
        $dag = new Dag();
        $generator = $workflow->execute();
        /** @var Timer|Task|Signal $item */
        $item = $generator->current();
        $generator->next();
        $lastNode = new Node($item);
        $dag->addNode($lastNode);

        while ($generator->valid()) {
            /** @var Timer|Task|Signal $item */
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
