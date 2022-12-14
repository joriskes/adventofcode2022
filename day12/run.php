<?php
require __DIR__ . '/../bootstrap.php';

$input = file_get_contents(__DIR__ . '/input.txt');
$lines = input_to_lines($input);

$start = [-1, -1];
$end = [-1, -1];
$heightMap = [];
$stepMapPart1 = [];
$stepMapPart2 = [];
foreach ($lines as $y => $line) {
    $heightMap[$y] = [];
    $stepMapPart1[$y] = [];
    $stepMapPart2[$y] = [];
    $chars = str_split($line);
    foreach ($chars as $x => $char) {
        $cc = ord($char) - 96;
        if ($cc > 0 && $cc < 27) {
            $heightMap[$y][$x] = $cc;
        } else {
            if ($char === 'S') {
                $heightMap[$y][$x] = 1;
                $start = [$x, $y];
            }
            if ($char === 'E') {
                $heightMap[$y][$x] = 26;
                $end = [$x, $y];
            }
        }
        $stepMapPart1[$y][$x] = -1;
        $stepMapPart2[$y][$x] = -1;
    }
}

// Recursive function that steps in cardinal positions if the position is valid (not too high)
// and was unvisited or visited with more steps than the current (can happen since depth first)
function nextStep($position, $stepMap, $stepCounter = 0, $part2 = false, $lowestSteps = -1)
{
    global $heightMap;
    $currentHeight = $heightMap[$position[1]][$position[0]];
    if ($part2) {
        // In part 2 we don't need to go further than the known lowest
        if ($stepCounter >= $lowestSteps) {
            return $stepMap;
        }
        // and when we visit an a (it will be a start later so always lower)
        if ($stepCounter > 0 && $currentHeight == 1) {
            return $stepMap;
        }
    }
    $stepMap[$position[1]][$position[0]] = $stepCounter;
//    p('Step (' . $stepCounter . ') to ' . $position[0] . ',' . $position[1] . ' @' . $currentHeight);
    $cardinals = [
        [$position[0] + 1, $position[1]],
        [$position[0] - 1, $position[1]],
        [$position[0], $position[1] + 1],
        [$position[0], $position[1] - 1],
    ];
    foreach ($cardinals as $cardinal) {
        if ($cardinal[1] >= 0 && $cardinal[1] < count($heightMap) && $cardinal[0] >= 0 && $cardinal[0] < count($heightMap[$position[1]])) {
            $targetHeight = $heightMap[$cardinal[1]][$cardinal[0]];
            $targetSteps = $stepMap[$cardinal[1]][$cardinal[0]];
            // Can we step there, and did we not visit or visit with more steps
            if ($targetHeight - 1 <= $currentHeight && ($targetSteps === -1 || $targetSteps > $stepCounter + 1)) {
                $stepMap = nextStep($cardinal, $stepMap, $stepCounter + 1, $part2, $lowestSteps);
            }
        }
    }
    return $stepMap;
}

$step = 0;
$position = [$start[0], $start[1]];
$stepMapPart1 = nextStep($position, $stepMapPart1);
$part1 = $stepMapPart1[$end[1]][$end[0]];

// Find all a's and check them
$toCheck = [];
foreach ($heightMap as $y => $line) {
    foreach ($line as $x => $height) {
        if ($height === 1) {
            $toCheck[] = [$x, $y];
        }
    }
}
$part2 = $part1 + 1;
foreach ($toCheck as $check) {
    foreach ($stepMapPart2 as $y => $line) {
        $stepMapPart2[$y] = array_fill(0, count($line), -1);
    }
    $stepMapPart2 = nextStep($check, $stepMapPart2, 0, true, $part2);
    $currentSteps = $stepMapPart2[$end[1]][$end[0]];
    if ($currentSteps > -1 && $currentSteps < $part2) {
        $part2 = $currentSteps;
    }
}

p('Part 1: ' . $part1);
p('Part 2: ' . $part2);
