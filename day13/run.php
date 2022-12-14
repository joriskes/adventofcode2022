<?php
require __DIR__ . '/../bootstrap.php';

$input = file_get_contents(__DIR__ . '/input.txt');
$lines = input_to_lines($input);

$pairs = [];
for ($i = 0; $i < count($lines); $i += 3) {
    $pairs[] = [json_decode($lines[$i]), json_decode($lines[$i + 1])];
}

// Returns a negative numeric value if left is lower
// a positive value if right is lower and zero when equal
function testPair($left, $right)
{
    if (is_numeric($left) && is_numeric($right)) {
        return intval($left) - intval($right);
    }

    if (is_array($left) && is_array($right)) {
        // If one (or both) run of out items, return
        if (count($left) === 0 || count($right) === 0) {
            return count($left) - count($right);
        }

        $ls = array_shift($left);
        $rs = array_shift($right);
        $res = testPair($ls, $rs);
        if ($res === 0) {
            return testPair($left, $right);
        }
        return $res;
    }

    if (is_numeric($left)) {
        return testPair([$left], $right);
    }
    if (is_numeric($right)) {
        return testPair($left, [$right]);
    }

    p('This should not happen');
    return 0;
}

$flatListPart2 = [];
$indeces_in_order = [];
foreach ($pairs as $index => $pair) {
    $flatListPart2[] = $pair[0];
    $flatListPart2[] = $pair[1];

    $res = testPair($pair[0], $pair[1]);
    if ($res < 0) {
        $indeces_in_order[] = $index + 1;
    }
}
$part1 = array_sum($indeces_in_order);
p('Part 1: ' . $part1);

$flatListPart2[] = [[2]];
$flatListPart2[] = [[6]];

usort($flatListPart2, 'testPair');

$i1 = 0;
$i2 = 0;
foreach ($flatListPart2 as $i => $l) {
    if ($l === [[2]]) {
        $i1 = $i + 1;
    }
    if ($l === [[6]]) {
        $i2 = $i + 1;
    }
}

$part2 = $i1 * $i2;
p('Part 2: ' . $part2);
