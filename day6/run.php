<?php
require __DIR__ . '/../bootstrap.php';

$input = file_get_contents(__DIR__ . '/input.txt');

function findDistinctSequence($input, $sequenceLength) {
    $markerChars = [];
    $chars = str_split($input);
    foreach ($chars as $index => $c) {
        while(in_array($c, $markerChars)) {
            array_shift($markerChars);
        }
        array_push($markerChars, $c);
        if(count($markerChars) === $sequenceLength) {
            return $index + 1; // 1 indexed string
        }
    }
    return -1;
}

$part1 = findDistinctSequence($input, 4);
p('Part 1: ' . $part1);

$part2 = findDistinctSequence($input, 14);
p('Part 2: ' . $part2);
