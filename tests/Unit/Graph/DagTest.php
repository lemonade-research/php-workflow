<?php

declare(strict_types=1);

namespace Lemonade\Workflow\Tests\Unit\Graph;

use Lemonade\Workflow\Graph\Dag;
use Lemonade\Workflow\Graph\Node;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;

class DagTest extends TestCase
{
    use ProphecyTrait;

    public function testAddAndRetrieveNodes()
    {
        $dag = $this->getUnitUnderTest();

        $node1 = $this->prophesize(Node::class)->reveal();
        $node2 = $this->prophesize(Node::class)->reveal();

        $dag->addNode($node1);
        $dag->addNode($node2);

        $this->assertCount(2, $dag->getNodes());
        $this->assertSame([$node1, $node2], $dag->getNodes());
    }

    public function testAddAndRetrieveEdges()
    {
        $dag = $this->getUnitUnderTest();

        $node1 = new Node(new \stdClass());
        $node2 = new Node(new \stdClass());
        $node3 = new Node(new \stdClass());

        $dag->addEdge($node1, $node2);
        $dag->addEdge($node2, $node3);

        $this->assertCount(2, $dag->getEdges());
        $this->assertSame([[$node1, $node2], [$node2, $node3]], $dag->getEdges());
    }

    public function testIsValid()
    {
        $dag = $this->getUnitUnderTest();
        $this->assertTrue($dag->isValid());
    }

    private function getUnitUnderTest(): Dag
    {
        return new Dag();
    }
}
