<?php
require __DIR__ . '/../bootstrap.php';

$input = file_get_contents(__DIR__ . '/input.txt');
$lines = input_to_lines($input);

$elves = [];
$currentElf = 0;
foreach ($lines as $l) {
    if(!isset($elves[$currentElf])) {
        $elves[$currentElf] = 0;
    }
    if($l == '') {
        $currentElf++;
    } else {
        $elves[$currentElf] += intval($l);
    }
}

sort($elves);
$part1 = $elves[count($elves) - 1];
$part2 = $elves[count($elves) - 1] + $elves[count($elves) - 2] + $elves[count($elves) - 3];

p('Part 1: ' . $part1);
p('Part 2: ' . $part2);
