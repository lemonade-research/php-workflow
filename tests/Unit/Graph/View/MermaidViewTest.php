<?php

declare(strict_types=1);

namespace Lemonade\Workflow\Tests\Unit\Graph\View;

use Lemonade\Workflow\DataStorage\Task;
use Lemonade\Workflow\Enum\TaskStatus;
use Lemonade\Workflow\Graph\Dag;
use Lemonade\Workflow\Graph\Node;
use Lemonade\Workflow\Graph\View\MermaidView;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Ramsey\Uuid\Uuid;

class MermaidViewTest extends TestCase
{
    use ProphecyTrait;

    /**
     * @test
     * @dataProvider provideDag
     */
    public function itShouldReturnExpectedData(Dag $dag, array $expected): void
    {
        $mermaid = new MermaidView();
        $actual = $mermaid->map($dag);

        $this->assertSame($expected, $actual);
    }

    public function provideDag(): array
    {
        $task1 = self::node('Task1');
        $task2 = self::node('Task2');

        return [
            'empty' => [
                'dag' => new Dag([], []),
                'expected' => ['style id0 fill:#f9f,stroke:#333,stroke-width:4px']
            ],
            'one node' => [
                'dag' => new Dag([new Node($task1)], []),
                'expected' => ['style id0 fill:#f9f,stroke:#333,stroke-width:4px']
            ],
            'two node' => [
                'dag' => new Dag([new Node($task1), new Node($task2)], [[0, 1]]),
                'expected' => ['id0(Task1) --> id1(Task2)', 'style id0 fill:#f9f,stroke:#333,stroke-width:4px']
            ],
        ];
    }

    private static function node(string $class): Task
    {
        return new Task(
            id: Uuid::uuid4(),
            class: $class,
            status: TaskStatus::INITIAL,
            value: null,
        );
    }
}
