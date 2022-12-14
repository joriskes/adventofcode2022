<?php
require __DIR__ . '/../bootstrap.php';

$input = file_get_contents(__DIR__ . '/input.txt');
$lines = input_to_lines($input);
$source = [500, 0];

function drawLine($from, $to, $cave)
{
    for ($x = min($from[0], $to[0]); $x <= max($from[0], $to[0]); $x++) {
        for ($y = min($from[1], $to[1]); $y <= max($from[1], $to[1]); $y++) {
            $cave[$y][$x] = '#';
        }
    }
    return $cave;
}

function drawCave($cave)
{
    foreach ($cave as $line) {
        foreach ($line as $c) {
            echo $c;
        }
        echo "\n";
    }
}

function dropSand($cave)
{
    global $source;
    $done = false;
    $position = [$source[0], $source[1]];
    while (!$done) {
        if ($position[1] >= count($cave) - 1) {
            // Falls of map
            return [true, $cave];
        }
        if ($cave[$position[1] + 1][$position[0]] === '.') {
            $position[1]++;
        } else {
            if ($cave[$position[1] + 1][$position[0] - 1] === '.') {
                $position[0]--;
                $position[1]++;
            } else {
                if ($cave[$position[1] + 1][$position[0] + 1] === '.') {
                    $position[0]++;
                    $position[1]++;
                } else {
                    $cave[$position[1]][$position[0]] = 'o';
                    $done = true;
                }
            }
        }
    }
    if ($position[0] === $source[0] && $position[1] === $source[1]) {
        return [true, $cave];
    }
    return [false, $cave];
}

function buildCave($linePoints, $caveWidth, $caveHeight, $addFloor = false)
{
    global $source;

    $cave = [];
    for ($y = 0; $y < $caveHeight + 2; $y++) {
        $cave[$y] = [];
        $sand[$y] = [];
        for ($x = $source[0] - round($caveWidth / 2); $x < $source[0] + round($caveWidth / 2); $x++) {
            if ($x === $source[0] && $y === $source[1]) {
                $cave[$y][$x] = '+';
            } else {
                $cave[$y][$x] = '.';
            }
        }
    }

    foreach ($linePoints as $points) {
        for ($i = 1; $i < count($points); $i++) {
            $cave = drawLine($points[$i - 1], $points[$i], $cave);
        }
    }

    if ($addFloor) {
        $floorY = count($cave);
        $cave[$floorY] = [];
        for ($x = $source[0] - round($caveWidth / 2); $x < $source[0] + round($caveWidth / 2); $x++) {
            $cave[$y][$x] = '#';
        }
    }
    return $cave;
}


$linePoints = [];
$caveHeight = 0;
$caveWidth = 400;
foreach ($lines as $line) {
    $pointStrings = explode(' -> ', $line);
    $points = [];
    foreach ($pointStrings as $pointString) {
        $p = array_map('intval', explode(',', $pointString));
        if ($p[1] > $caveHeight) {
            $caveHeight = $p[1];
        }
        $points[] = $p;
    }
    $linePoints[] = $points;
}

$cave = buildCave($linePoints, $caveWidth, $caveHeight);
$done = false;
$part1 = 0;
while (!$done) {
    [$done, $cave] = dropSand($cave);
    // Last sand fell off map, so don't count that one
    if (!$done) $part1++;
}
//drawCave($cave);
p('Part 1: ' . $part1);

$cave = buildCave($linePoints, $caveWidth, $caveHeight, true);
$done = false;
$part2 = 0;
while (!$done) {
    [$done, $cave] = dropSand($cave);
    $part2++;
}
//drawCave($cave);
p('Part 2: ' . $part2);
