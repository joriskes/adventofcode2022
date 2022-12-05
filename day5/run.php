<?php
require __DIR__ . '/../bootstrap.php';

$input = file_get_contents(__DIR__ . '/input.txt');
$lines = explode("\n", $input);

$stacksPart1 = [];
$stackLines = [];
$foundStacks = false;

// Shift off the stack lines
while(!$foundStacks) {
    if(strpos($lines[0], '[') === false) {
        $foundStacks = true;
    } else {
        $stackLines[] = array_shift($lines);
    }
}

// Grab the stack numbers
$countLine = array_shift($lines);
$stackNumbers = explode(' ', trim($countLine));
$stackCount = intval(trim(array_pop($stackNumbers)));

// Skip the empty line
if(empty(trim($lines[0]))) array_shift($lines);

// Read the stack lines and put them in the stack array
foreach ($stackLines as $stackLine) {
    for($i=0; $i<$stackCount; $i++) {
        $s = substr($stackLine, $i * 4, 4);
        if(!isset($stacksPart1[$i])) {
            $stacksPart1[$i] = [];
        }
        if(strlen(trim($s) > 1)) {
            array_unshift($stacksPart1[$i], substr($s, 1, 1));
        }
    }
}

function moveCrateMover9000($stacks, $from, $to) {
    $crate = array_pop($stacks[$from - 1]);
    array_push($stacks[$to - 1], $crate);
    return $stacks;
}

function moveCrateMover9001($stacks, $count, $from, $to) {
    $crates = [];
    for($i=0; $i<$count; $i++) {
        $crates[] = array_pop($stacks[$from - 1]);
    }
    while(count($crates) > 0) {
        $stacks[$to - 1][] = array_pop($crates);
    }
    return $stacks;
}

// Deep copy stack state
$stacksPart2 = unserialize(serialize($stacksPart1));

// Now that the initial state is set, start moving
foreach ($lines as $move) {
    if(!empty(trim($move))) {
        preg_match_all('/move (\d+) from (\d+) to (\d+)/', $move, $matches);
        if(count($matches) > 3) {
            $count = $matches[1][0];
            $from = $matches[2][0];
            $to = $matches[3][0];

            for($i=0; $i<$count; $i++) {
                $stacksPart1 = moveCrateMover9000($stacksPart1, $from, $to);
            }
            $stacksPart2 = moveCrateMover9001($stacksPart2, $count, $from, $to);
        }
    }
}

$part1 = '';
foreach ($stacksPart1 as $stack) {
    $part1.=$stack[count($stack)-1];
}

$part2 = '';
foreach ($stacksPart2 as $stack) {
    $part2.=$stack[count($stack)-1];
}

p('Part 1: ' . $part1);
p('Part 2: ' . $part2);
