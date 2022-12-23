<?php
require __DIR__ . '/../bootstrap.php';

$input = trim(file_get_contents(__DIR__ . '/input.txt'));
// $input = '>>><<><>><<<>><>>><<<>>><<<><<<>><>><<>>';

define("ROOMWIDTH", 7);
define("NEEDEDROCKS_PART1", 2022);
define("NEEDEDROCKS_PART2", 1000000000000);

class Rock
{
    static $shapes = [
        ['####'],
        [' # ', '###', ' # '],
        ['  #', '  #', '###'],
        ['#', '#', '#', '#'],
        ['##', '##']
    ];
    static $nextIndex = 0;

    public $shapeIndex;
    public $x;
    public $y;
    public $active = false;

    public function next($x, $y)
    {
        $this->shapeIndex = self::$nextIndex;
        self::$nextIndex++;
        if (self::$nextIndex >= count(self::$shapes)) {
            self::$nextIndex = 0;
        }
        $this->x = $x;
        $this->y = $y;
        $this->active = true;
    }

    public function isCollision($offsetX, $offsetY, $room)
    {
        $rockX = $this->x + $offsetX;
        $rockY = $this->y + $offsetY;
        $shape = $this->getShape();
        if ($rockX < 0) {
            return true;
        }
        if ($rockX + strlen($shape[0]) > ROOMWIDTH) {
            return true;
        }

        // Check only lines the rock is in
        for ($y = min(count($room), $rockY + count($shape)) - 1; $y >= min(count($room), $rockY); $y--) {
            $i = $rockY - $y + (count($shape) - 1);
            for ($x = 0; $x < strlen($shape[$i]); $x++) {
                if (($shape[$i][$x] !== ' ') && ($room[$y][$x + $rockX] !== ' ')) {
                    return true;
                }
            }
        }
        return false;
    }

    public function getShape()
    {
        return self::$shapes[$this->shapeIndex];
    }

    public function place($room, $drawMode = false)
    {
        $shape = $this->getShape();
        $roomHeight = count($room);
        $isWriting = false;
        for ($y = $roomHeight + 5; $y > -1; $y--) {
            $newLine = implode(array_fill(0, ROOMWIDTH, ' '));
            $l = $newLine;
            if ($y < $roomHeight) {
                $l = $room[$y];
            }
            if ($this->active) {
                if ($y >= $this->y && $y < $this->y + count($shape)) {
                    $i = $this->y - $y + (count($shape) - 1);
                    for ($x = 0; $x < strlen($shape[$i]); $x++) {
                        if ($shape[$i][$x] !== ' ') {
                            $l[$x + $this->x] = $drawMode ? '@' : '#';
                            $isWriting = true;
                        }
                    }
                }
            }
            if ($isWriting || $drawMode) {
                $room[$y] = $l;
            }
        }
        return $room;
    }
}

function draw($room, $rock)
{
    $roomToDraw = $rock->place($room, true);
    for ($y = count($roomToDraw) - 1; $y > -1; $y--) {
        $line = $roomToDraw[$y];
        p('|' . $line . '|');
    }
}

$room = [];
$room[0] = implode(array_fill(0, ROOMWIDTH, '-'));

$rockCounter = 0;
$rock = new Rock();
$activeJetIndex = -1;
$part2Correction = 0;
$cache = [];

while (1) {
    if (!$rock->active) {
        $roomHeight = count($room) - 1;
        if ($rockCounter === NEEDEDROCKS_PART1) {
            $part1 = $roomHeight;
        }
        if ($rockCounter === NEEDEDROCKS_PART2) {
            $part2 = $roomHeight + $part2Correction;
            break;
        }
        // For part 2 we build a cache
        $key = '';
        for ($i = $roomHeight; $i > max(0, $roomHeight - 100); $i--) {
            $key .= $room[$i];
        }
        $key = md5($key) . '-' . $rock->shapeIndex . '-' . $activeJetIndex;
        if (isset($cache[$key])) {
            // And if we find a colision we can step
            $stepSize = $rockCounter - $cache[$key][1];
            $roomIncrease = $roomHeight - $cache[$key][0];
            while ($rockCounter < NEEDEDROCKS_PART2) {
                $rockCounter += $stepSize;
                $part2Correction += $roomIncrease;
            }
            // Do the last rocks manually again
            $rockCounter -= $stepSize;
            $part2Correction -= $roomIncrease;
        } else {
            $cache[$key] = [$roomHeight, $rockCounter];
        }
        $rockCounter++;
        $rock->next(2, count($room) + 3);
    }
    $activeJetIndex++;
    if ($activeJetIndex >= strlen($input)) {
        $activeJetIndex = 0;
    }
    $jet = $input[$activeJetIndex];
    if (($jet === '>') && !$rock->isCollision(1, 0, $room)) {
        $rock->x++;
    }
    if (($jet === '<') && !$rock->isCollision(-1, 0, $room)) {
        $rock->x--;
    }
    if (!$rock->isCollision(0, -1, $room)) {
        $rock->y--;
    } else {
        $room = $rock->place($room);
        $rock->active = false;
    }
}

p('Part 1: ' . $part1);
p('Part 2: ' . $part2);
