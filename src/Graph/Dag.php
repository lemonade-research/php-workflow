<?php

declare(strict_types=1);

namespace Lemonade\Workflow\Graph;

class Dag
{
    /** @var Node[] $nodes */
    private array $nodes;
    /** @var array<Node[]> $edges */
    private array $edges;

    public function __construct()
    {
    }

    public function addNode(Node $node): void
    {
        $this->nodes[] = $node;
    }

    public function addEdge(Node $from, Node $to): void
    {
        $this->edges[] = [$from, $to];
    }

    /**
     * @return Node[]
     */
    public function getNodes(): array
    {
        return $this->nodes;
    }

    /**
     * @return array<int, Node[]>
     */
    public function getEdges(): array
    {
        return $this->edges;
    }

    public function isValid(): bool
    {
        return true;
    }
}
