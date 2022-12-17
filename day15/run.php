<?php
require __DIR__ . '/../bootstrap.php';

$input = file_get_contents(__DIR__ . '/input.txt');
$yInQuestion = 2000000;
$size = 4000000;

function ManhattanDistance($p1, $p2)
{
    return abs($p1[0] - $p2[0]) + abs($p1[1] - $p2[1]);
}

class Sensor
{
    private $position;
    private $beacon;
    private $radius;

    public function __construct($px, $py, $bx, $by)
    {
        $this->position = [intval($px), intval($py)];
        $this->beacon = [intval($bx), intval($by)];

        $this->radius = ManhattanDistance($this->position, $this->beacon);
    }

    public function leftMostX($y)
    {
        // Could be smarter
        return $this->position[0] - $this->radius;
    }

    public function rightMostX($y)
    {
        // Could be smarter
        return $this->position[0] + $this->radius;
    }

    public function isExcluded($x, $y, $dontReportBeacons = false)
    {
        if ((!$dontReportBeacons) && ($this->beacon === [$x, $y])) return false;
        $dist = ManhattanDistance($this->position, [$x, $y]);
        $excluded = $dist <= $this->radius;
        $skipX = 0;
        if ($excluded) {
            // Figure out how much X we can skip
            if ($x < $this->position[0]) {
                // If we're to the left of the position:
                // Skip X until we're on the x mirrored on sensor position Y, everything in between is excluded anyway
                $skipX = max(0, (($this->position[0] - $x) * 2) - 1);
            } else if ($x > $this->position[0]) {
                // If we're on the right
                // We can skip the radius minus the distance
                $skipX = max(0, $this->radius - $dist - 1);
            }
        }
        return [$excluded, $skipX];
    }

    public function __toString(): string
    {
        return 'S:' . implode(',', $this->position) . ' B:' . implode(',', $this->beacon) . ' R:' . $this->radius;
    }
}

$lines = input_to_lines($input);
$sensors = [];
foreach ($lines as $line) {
    preg_match_all('/^Sensor at x=(-?\d+), y=(-?\d+): closest beacon is at x=(-?\d+), y=(-?\d+)$/i', $line, $matches);
    $sensors[] = new Sensor($matches[1][0], $matches[2][0], $matches[3][0], $matches[4][0]);
}

$leftX = PHP_INT_MAX;
$rightX = -PHP_INT_MAX;
foreach ($sensors as $sensor) {
    $leftX = min($sensor->leftMostX($yInQuestion), $leftX);
    $rightX = max($sensor->rightMostX($yInQuestion), $rightX);
}

$part1 = 0;
$x = $leftX;
while ($x <= $rightX) {
    foreach ($sensors as $sensor) {
        [$excluded, $skipX] = $sensor->isExcluded($x, $yInQuestion);
        if ($excluded) {
            $x += $skipX;
            $part1 += $skipX + 1;
            break;
        }
    }
    $x++;
}
p('Part 1: ' . $part1);

$found = [];
for ($y = 0; $y <= $size; $y++) {
    $x = 0;
    while ($x <= $size) {
        $someExcluded = false;
        $maxSkip = 0;
        foreach ($sensors as $sensor) {
            [$excluded, $skipX] = $sensor->isExcluded($x, $y, true);
            if ($excluded) {
                $maxSkip = max($maxSkip, $skipX);
                $someExcluded = true;
            }
        }
        if (!$someExcluded) {
            // Found! done
            $found = [$x, $y];
            break 2;
        }
        $x += $maxSkip + 1;
    }
}
$part2 = ($found[0] * 4000000) + $found[1];
p('Part 2: ' . $part2);
