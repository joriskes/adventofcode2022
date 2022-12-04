<?php
require __DIR__ . '/../bootstrap.php';

$input = file_get_contents(__DIR__ . '/input.txt');
$lines = input_to_lines($input);

$part1 = 0;
$part2 = 0;
foreach ($lines as $line) {
    [$left, $right] = explode(',', $line);
    [$from1, $to1] = array_map('intval', explode('-', $left));
    [$from2, $to2] = array_map('intval', explode('-', $right));

    if ($from1 >= $from2 && $to1 <= $to2) {
        $part1++;
    } else
        if ($from2 >= $from1 && $to2 <= $to1) {
            $part1++;
        }

    if ($from1 >= $from2 && $from1 <= $to2) {
        $part2++;
    } else
        if ($from2 >= $from1 && $from2 <= $to1) {
            $part2++;
        } else
            if($to1 >= $from2 && $to1 <= $to2) {
                $part2++;
            } else {
                if($to2 >=$from1 && $to2 <= $to1) {
                    $part2++;
                }
            }
}

p('Part 1: ' . $part1);
p('Part 2: ' . $part2);
