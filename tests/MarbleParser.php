<?php

declare(strict_types=1);

namespace Lemonade\Workflow\Tests;

use Lemonade\Workflow\Tests\Model\Notification;

class MarbleParser
{
    private const TIME_FACTOR = 10;

    private int $nextFrame;

    /**
     * @param string $marbleString
     *
     * @return Notification[]
     */
    public function parse(string $marbleString): array
    {
        $len          = strlen($marbleString);
        $testMessages = [];
        $frame        = 0;
        $groupStart   = -1;

        for ($i = 0; $i < $len; $i++) {
            $this->nextFrame = $frame;
            $advanceFrameBy  = function (int $count): void {
                $this->nextFrame += $count * self::TIME_FACTOR;
            };

            $char = $marbleString[$i];
            $notification = null;
            switch ($char) {
                case ' ':
                    break;
                case '-':
                case '^':
                    $advanceFrameBy(1);
                    break;
                case '(':
                    $groupStart = $frame;
                    $advanceFrameBy(1);
                    break;
                case ')':
                    $groupStart = -1;
                    $advanceFrameBy(1);
                    break;
                case '|':
                    $advanceFrameBy(1);
                    $notification = Notification::complete((int) ($groupStart > -1 ? $groupStart : $frame));
                    break;
                case '#':
                    $advanceFrameBy(1);
                    $notification = Notification::error((int) ($groupStart > -1 ? $groupStart : $frame), '');
                    break;
                default:
                    if (preg_match('/\d/', $char) === 1) {
                        if ($i === 0 || $marbleString[$i - 1] === ' ') {
                            $buffer = substr($marbleString, $i);
                            $match  = preg_match('/^(\d+(?:\.\d+)?)(ms|s|m) /', $buffer, $matches);
                            if ($match === 1) {
                                $i            += strlen($matches[0]) - 1;
                                $duration     = (float) $matches[1];
                                $unit         = $matches[2];
                                $durationInMs = 1;

                                switch ($unit) {
                                    case 'ms':
                                        $durationInMs = $duration;
                                        break;
                                    case 's':
                                        $durationInMs = $duration * 1000;
                                        break;
                                    case 'm':
                                        $durationInMs = $duration * 1000 * 60;
                                        break;
                                    case 'h':
                                        $durationInMs = $duration * 1000 * 60 * 60;
                                        break;
                                    case 'd':
                                        $durationInMs = $duration * 1000 * 60 * 60 * 24;
                                        break;
                                    default:
                                        break;
                                }

                                $advanceFrameBy((int)$durationInMs / self::TIME_FACTOR);
                                break;
                            }
                        }
                    }
                    $advanceFrameBy(1);
                    $notification = Notification::next((int) ($groupStart > -1 ? $groupStart : $frame), $char);
            }

            if ($notification) {
                $testMessages[] = $notification;
            }

            $frame = $this->nextFrame;
        }

        return $testMessages;
    }
}
