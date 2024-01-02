<?php

declare(strict_types=1);

namespace Lemonade\Workflow\Tests\Unit\Graph;

use Carbon\Carbon;
use Lemonade\Workflow\Graph\Dag;
use Lemonade\Workflow\Graph\Node;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;

class DagTest extends TestCase
{
    use ProphecyTrait;

    /**
     * @test
     */
    public function itShouldAddAndRetrieveNodes()
    {
        $dag = $this->getUnitUnderTest();

        $node1 = $this->prophesize(Node::class)->reveal();
        $node2 = $this->prophesize(Node::class)->reveal();

        $dag->addNode($node1);
        $dag->addNode($node2);

        $this->assertCount(2, $dag->getNodes());
        $this->assertSame([$node1, $node2], $dag->getNodes());
    }

    /**
     * @test
     */
    public function itShouldAddAndRetrieveEdges()
    {
        $node1 = new Node(new \stdClass());
        $node2 = new Node(new \stdClass());
        $node3 = new Node(new \stdClass());

        $dag = $this->getUnitUnderTest();
        $dag->addNode($node1);
        $dag->addNode($node2);
        $dag->addNode($node3);
        $dag->addEdge($node1, $node2);
        $dag->addEdge($node2, $node3);

        $this->assertCount(2, $dag->getEdges());
        $this->assertSame([[0, 1], [1, 2]], $dag->getEdges());
    }

    /**
     * @test
     */
    public function itShouldThrowExceptionIfNodeDoesNotExist()
    {
        $this->expectException(\InvalidArgumentException::class);

        $node1 = new Node(new \stdClass());
        $node2 = new Node(new \stdClass());

        $dag = $this->getUnitUnderTest();
        $dag->addEdge($node1, $node2);
    }

    /**
     * @test
     */
    public function itShouldReturnCurrentActiveNode()
    {
        $start = $this->prophesize(Node::class)->reveal();
        $second = $this->prophesize(Node::class)->reveal();

        $dag = $this->getUnitUnderTest();
        $dag->addNode($start);
        $dag->addNode($second);


        $this->assertSame($start, $dag->current());
    }

    /**
     * @test
     */
    public function itShouldThrowExceptionIfNodeDoesNotExistForNextCall()
    {
        $this->expectException(\InvalidArgumentException::class);

        $node = new Node(new \stdClass());

        $dag = $this->getUnitUnderTest();
        $dag->next($node);
    }

    /**
     * @test
     * @dataProvider provideNextNodes
     */
    public function itShouldReturnNextNodes(Dag $dag, Node $current, array $expected): void
    {
        $this->assertSame($expected, $dag->next($current));
    }

    public static function provideNextNodes(): array
    {
        $dag = new Dag();
        $node1 = new Node(new \stdClass());
        $node2 = new Node(new \stdClass());
        $node3 = new Node(new \stdClass());
        $node4 = new Node(new \stdClass());
        $dag->addNode($node1);
        $dag->addNode($node2);
        $dag->addNode($node3);
        $dag->addNode($node4);
        $dag->addEdge($node1, $node2);
        $dag->addEdge($node2, $node4);
        $dag->addEdge($node1, $node3);
        $dag->addEdge($node3, $node4);

        return [
            'first node' => [
                $dag,
                $node1,
                [$node2, $node3],
            ],
            'second node' => [
                $dag,
                $node2,
                [$node4],
            ],
            'third node' => [
                $dag,
                $node3,
                [$node4],
            ],
        ];
    }

    private function getUnitUnderTest(): Dag
    {
        return new Dag();
    }
}
