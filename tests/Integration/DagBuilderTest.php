<?php

declare(strict_types=1);

namespace Lemonade\Workflow\Tests\Integration;

use Lemonade\Workflow\Graph\DagBuilder;
use Lemonade\Workflow\Tests\Integration\Fixture\ExampleWorkflow;
use PHPUnit\Framework\TestCase;

class DagBuilderTest extends TestCase
{
    /**
     * @test
     */
    public function itShouldBuildDag(): void
    {
        $subject = $this->getUnitUnderTest();
        $dag = $subject->build(new ExampleWorkflow());

        $this->assertCount(3, $dag->getNodes());
        $this->assertCount(2, $dag->getEdges());
    }

    private function getUnitUnderTest(): DagBuilder
    {
        return new DagBuilder();
    }
}
