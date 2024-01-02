<?php

declare(strict_types=1);

namespace Lemonade\Workflow\Graph;

/**
 * @template T of object
 */
class Dag
{
    public function __construct(
        /** @var Node<T>[] $nodes */
        private array $nodes = [],
        /** @var array<int[]> $edges */
        private array $edges = [],
        public int $index = 0,
    ) {
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
        $fromIndex = array_search($from, $this->nodes, true);
        $toIndex = array_search($to, $this->nodes, true);

        if ($fromIndex === false || $toIndex === false) {
            throw new \InvalidArgumentException('Node not found');
        }

        $this->edges[] = [(int)$fromIndex, (int)$toIndex];
    }

    /**
     * @return Node<T>[]
     */
    public function getNodes(): array
    {
        return $this->nodes;
    }

    /**
     * @return array<int[]>
     */
    public function getEdges(): array
    {
        return $this->edges;
    }

    /**
     * @return Node<T>
     */
    public function current(): Node
    {
        return $this->nodes[$this->index];
    }


    /**
     * @param Node<T> $current
     * @return Node<T>[]
     */
    public function next(Node $current): array
    {
        $index = array_search($current, $this->nodes, true);

        if ($index === false) {
            throw new \InvalidArgumentException('Node not found');
        }

        return array_values(array_map(
            fn(array $edge) => $this->nodes[$edge[1]],
            array_filter($this->edges, fn(array $edge) => $edge[0] === $index)
        ));
    }

    /**
     * @param Node<T> $current
     */
    public function nextIndex(Node $current): void
    {
        /** @var int|false $index */
        $index = array_search($current, $this->nodes, true);

        if ($index === false) {
            throw new \InvalidArgumentException('Node not found');
        }

        $this->index = $index;
    }

    public function __toString(): string
    {
        $nodes = array_map(fn(Node $node) => $node->item::class, $this->nodes);
        $edges = array_map(fn(array $edge) => $edge[0] . ' -> ' . $edge[1], $this->edges);

        return implode("\n", array_merge($nodes, $edges));
    }
}
