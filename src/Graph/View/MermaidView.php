<?php

declare(strict_types=1);

namespace Lemonade\Workflow\Graph\View;

use Lemonade\Workflow\DataStorage\Signal;
use Lemonade\Workflow\DataStorage\Task;
use Lemonade\Workflow\DataStorage\Timer;
use Lemonade\Workflow\Graph\Dag;
use Lemonade\Workflow\Graph\DagBuilder;
use Lemonade\Workflow\Graph\Node;

/**
 * @phpstan-import-type DagTypes from DagBuilder
 */
class MermaidView
{
    /**
     * @param Dag<DagTypes> $dag
     * @return array<string>
     */
    public function map(Dag $dag): array
    {
        $list = array_map(
            fn (array $edge) => sprintf(
                '%s --> %s',
                $this->mapNode($dag->getNodes()[$edge[0]], $edge[0]),
                $this->mapNode($dag->getNodes()[$edge[1]], $edge[1]),
            ),
            $dag->getEdges()
        );
        $list[] = sprintf('style id%s fill:#f9f,stroke:#333,stroke-width:4px', $dag->index);

        return $list;
    }

    /**
     * @param Node<DagTypes> $node
     * @return string
     */
    private function mapNode(Node $node, int $index): string
    {
        $item = $node->item;

        return match (true) {
            $item instanceof Signal => sprintf('id%d([%s, %s])', $index, $item->name, var_export($item->predicateResult, true)),
            $item instanceof Task => sprintf('id%d(%s)', $index, $item->class),
            $item instanceof Timer =>  sprintf('id%d{{%s s}}', $index, $item->seconds),
        };
    }
}
