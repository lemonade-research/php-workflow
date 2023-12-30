<?php

declare(strict_types=1);

namespace Lemonade\Workflow\Tests\Integration;

use Lemonade\Workflow\Graph\DagBuilder;
use Lemonade\Workflow\Tests\Integration\Fixture\ExamplePayload;
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
        $dag = $subject->build(new ExampleWorkflow(), new ExamplePayload());

        $this->assertCount(5, $dag->getNodes());
        $this->assertCount(4, $dag->getEdges());
    }

    private function getUnitUnderTest(): DagBuilder
    {
        return new DagBuilder();
    }
}
