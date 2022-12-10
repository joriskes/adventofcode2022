<?php
require __DIR__ . '/../bootstrap.php';

$input = file_get_contents(__DIR__ . '/input.txt');
$lines = input_to_lines($input);

$trees = [];
foreach ($lines as $line) {
    $trees[] = str_split($line);
}

function calcLineTreeScore($line, $tree)
{
    if (!$line || count($line) < 1) {
        return 0;
    }
    $index = 0;
    $res = 0;
    $done = false;
    while ($index < count($line) && !$done) {
        $res++;
        if ($line[$index] >= $tree) {
            $done = true;
        }
        $index++;
    }
    return $res;
}

$part1 = 0;
$part2 = 0;
foreach ($trees as $y => $line) {
    foreach ($line as $x => $value) {
        $tree = intval($value);
        if ($y == 0 || $y == count($trees) - 1 || $x == 0 || $x == count($line) - 1) {
            // Outer trees are always visible for part1 and useless for part 2
            $part1++;
        } else {
            $lineX1 = array_reverse(array_slice($line, 0, $x));
            $lineX2 = array_slice($line, $x + 1, count($line) - $x);
            $column = array_column($trees, $x);
            $lineY1 = array_reverse(array_slice($column, 0, $y));
            $lineY2 = array_slice($column, $y + 1, count($column) - $y);

            $treeScore = 1;
            $treeScore *= calcLineTreeScore($lineX1, $tree);
            $treeScore *= calcLineTreeScore($lineX2, $tree);
            $treeScore *= calcLineTreeScore($lineY1, $tree);
            $treeScore *= calcLineTreeScore($lineY2, $tree);
            if ($treeScore > $part2) $part2 = $treeScore;

            rsort($lineX1);
            rsort($lineX2);
            rsort($lineY1);
            rsort($lineY2);
            if (($lineX1[0] < $tree) || ($lineX2[0] < $tree) || ($lineY1[0] < $tree) || ($lineY2[0] < $tree)) {
                $part1++;
            }
        }
    }
}


p('Part 1: ' . $part1);
p('Part 2: ' . $part2);
