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
for($i=0; $i<count($lines); $i+=3) {
    $groups = [];
    $groups[] = str_split($lines[$i]);
    $groups[] = str_split($lines[$i+1]);
    $groups[] = str_split($lines[$i+2]);

    foreach ($groups as $index=>$group) {
        sort($group);
        $groups[$index] = $group;
    }

    $found = false;
    $a = array_shift($groups[0]);
    $b = array_shift($groups[1]);
    $c = array_shift($groups[2]);
    while(!$found) {
        if($a === $b && $b === $c) {
            $found = true;
            $part2+= charScore($a);
        } else {
            if(ord($b) < ord($a)) {
                $b = array_shift($groups[1]);
            } else
            if(ord($c) < ord($a)) {
                $c = array_shift($groups[2]);
            } else {
                $a = array_shift($groups[0]);
            }
        }
    }
}

p('Part 1: ' . $part1);
p('Part 2: ' . $part2);
