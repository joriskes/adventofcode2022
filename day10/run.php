<?php
require __DIR__ . '/../bootstrap.php';

$input = file_get_contents(__DIR__ . '/input.txt');
$lines = input_to_lines($input);

$cycle = 0;
$regX = 1;
$toAdd = null;
$toAddDelay = 0;
$done = false;

$interestingCycles = [20, 60, 100, 140, 180, 220];
$part1 = 0;
$part2 = '';

while (!$done) {
    if ($cycle > 0 && $cycle % 40 == 0) {
        $part2 .= "\n";
    }
    if (($cycle % 40) >= $regX - 1 && ($cycle % 40) <= $regX + 1) {
        $part2 .= '#';
    } else {
        $part2 .= '.';
    }

    $cycle++;
    if ($toAdd === null) {
        if (count($lines) === 0) {
            $done = true;
        } else {
            $line = array_shift($lines);
            $cmd = explode(' ', $line);
            switch ($cmd[0]) {
                case 'noop':
                    //
                    break;
                case 'addx':
                    $toAdd = intval($cmd[1]);
                    $toAddDelay = 2;
                    break;
            }
        }
    }
    if (in_array($cycle, $interestingCycles)) {
        $part1 += $cycle * $regX;
    }

    if ($toAdd !== null) {
        $toAddDelay--;
        if ($toAddDelay == 0) {
            $regX += $toAdd;
            $toAdd = null;
        }
    }
}


p('Part 1: ' . $part1);
p('Part 2: ' . "\n" . $part2);

##..##..#..##...##.##..##..##..##..##..
##..##..##..##..##..##..##..##..##..##..
