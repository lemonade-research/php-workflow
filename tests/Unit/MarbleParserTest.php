<?php

declare(strict_types=1);

namespace Lemonade\Workflow\Tests\Unit;

use Lemonade\Workflow\Tests\MarbleParser;
use Lemonade\Workflow\Tests\Model\Notification;
use PHPUnit\Framework\TestCase;

class MarbleParserTest extends TestCase
{
    /**
     * @test
     * @dataProvider dataForTest
     *
     * @param string $marbleString
     * @param Notification[]  $expected
     */
    public function itShouldParseMarbleString(string $marbleString, array $expected): void
    {
        $parser = $this->getUnitUnderTest();
        $actual = $parser->parse($marbleString);

        $this->assertEquals($expected, $actual);
    }

    public static function dataForTest(): array
    {
        return [
            'should parse a marble string into a series of notifications' => [
                '-------a---b---|',
                [Notification::next(70, 'a'), Notification::next(110, 'b'), Notification::complete(150)],
            ],
            'allowing spaces too' => [
                '--a--b--|   ',
                [Notification::next(20, 'a'), Notification::next(50, 'b'), Notification::complete(80)],
            ],
            'marble string with an error' => [
                '-------a---b---#',
                [Notification::next(70, 'a'), Notification::next(110, 'b'), Notification::error(150, '')],
            ],
            'handle grouped values' => [
                '---(abc)---',
                [Notification::next(30, 'a'), Notification::next(30, 'b'), Notification::next(30, 'c')],
            ],
            'suppport time progression syntax' => [
                '10ms a 1.2s b 1m c|',
                [
                    Notification::next(10, 'a'),
                    Notification::next((int)(10 + 10 + (1.2 * 1000)), 'b'),
                    Notification::next((int)(10 + 10 + (1.2 * 1000) + 10 + (1000 * 60)), 'c'),
                    Notification::complete((int)(10 + 10 + (1.2 * 1000) + 10 + (1000 * 60) + 10)),
                ],
            ],
        ];
    }

    private function getUnitUnderTest(): MarbleParser
    {
        return new MarbleParser();
    }
}
