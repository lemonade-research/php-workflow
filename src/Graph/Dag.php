<?php

declare(strict_types=1);

namespace Lemonade\Workflow\Graph;

/**
 * @template T
 */
class Dag
{
    /** @var Node<T>[] $nodes */
    private array $nodes;
    /** @var array<Node<T>[]> $edges */
    private array $edges;

    public function __construct()
    {
    }

    /**
     * @param Node<T> $node
     */
    public function addNode(Node $node): void
    {
        $this->nodes[] = $node;
    }

    /**
     * @param Node<T> $from
     * @param Node<T> $to
     */
    public function addEdge(Node $from, Node $to): void
    {
        $this->edges[] = [$from, $to];
    }

    /**
     * @return Node<T>[]
     */
    public function getNodes(): array
    {
        return $this->nodes;
    }

    /**
     * @return array<int, Node<T>[]>
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
