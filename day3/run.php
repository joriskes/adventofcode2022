<?php
require __DIR__ . '/../bootstrap.php';

$input = file_get_contents(__DIR__ . '/input.txt');
$lines = input_to_lines($input);

function charScore($char) {
    if(strtolower($char) === $char) {
        return ord($char) - 96;
    } else {
        return ord($char) - 38;
    }
}

$part1 = 0;
foreach ($lines as $l) {
    $left = str_split(substr($l, 0, strlen($l) / 2));
    $right = str_split(substr($l, -strlen($l) / 2));

    $char = '';
    foreach ($left as $lc) {
        foreach ($right as $rc) {
            if($lc === $rc) {
                $char = $lc;
            }
        }
    }
    $part1+= charScore($char);
}

$part2 = 0;
$groupSize = 3;
for($i=0; $i<count($lines); $i+=$groupSize) {
    $groups = [];
    // Grab, split and sort the groups
    for($j=0; $j<$groupSize; $j++) {
        $lineAr = str_split($lines[$i+$j]);
        sort($lineAr);
        $groups[] = $lineAr;
    }

    $found = false;
    $chrs = [];
    // Shift the first char off each group
    for($j=0; $j<$groupSize; $j++) {
        $chrs[$j] = array_shift($groups[$j]);
    }

    // Search
    while(!$found) {
        $c = $chrs[0];
        $found = true;
        // See if we're done
        for($j=0; $j<$groupSize; $j++) {
            if($chrs[$j] !== $c) {
                $found = false;
            }
        }
        if($found) {
            // Done, tally score
            $part2+= charScore($c);
        } else {
            // Not done, shift the lowest char code of the group
            $lowest = 999;
            $lowestIndex = -1;
            for($j=0; $j<$groupSize; $j++) {
                if(ord($chrs[$j]) < $lowest) {
                    $lowest = ord($chrs[$j]);
                    $lowestIndex = $j;
                }
            }
            $chrs[$lowestIndex] = array_shift($groups[$lowestIndex]);
        }
    }
}

p('Part 1: ' . $part1);
p('Part 2: ' . $part2);
