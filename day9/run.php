<?php
require __DIR__ . '/../bootstrap.php';

$input = file_get_contents(__DIR__ . '/input.txt');

$lines = input_to_lines($input);

$rope = [];
for ($i = 0; $i < 10; $i++) {
    $rope[$i] = [0, 0];
}
$visitedPositionsPart1 = [];
$visitedPositionsPart2 = [];

array_push($visitedPositionsPart1, implode(',', $rope[1]));
array_push($visitedPositionsPart2, implode(',', $rope[9]));

foreach ($lines as $line) {
    [$dir, $count] = explode(' ', $line);
    while ($count > 0) {
        switch ($dir) {
            case 'U':
                $rope[0] = [$rope[0][0], $rope[0][1] - 1];
                break;
            case 'D':
                $rope[0] = [$rope[0][0], $rope[0][1] + 1];
                break;
            case 'L':
                $rope[0] = [$rope[0][0] - 1, $rope[0][1]];
                break;
            case 'R':
                $rope[0] = [$rope[0][0] + 1, $rope[0][1]];
                break;
        }
        for ($i = 1; $i < count($rope); $i++) {
            $dx = $rope[$i][0] - $rope[$i - 1][0];
            $dy = $rope[$i][1] - $rope[$i - 1][1];

            if (abs($dx) > 1 && abs($dy) > 1) {
                // Diagonal movement is possible in part 2
                if ($dx > 1) {
                    $rope[$i][0]--;
                }
                if ($dx < -1) {
                    $rope[$i][0]++;
                }
                if ($dy > 1) {
                    $rope[$i][1]--;
                }
                if ($dy < -1) {
                    $rope[$i][1]++;
                }
            } else {
                if ($dx > 1) {
                    $rope[$i][0]--;
                    $rope[$i][1] = $rope[$i - 1][1];
                }
                if ($dx < -1) {
                    $rope[$i][0]++;
                    $rope[$i][1] = $rope[$i - 1][1];
                }
                if ($dy > 1) {
                    $rope[$i][1]--;
                    $rope[$i][0] = $rope[$i - 1][0];
                }
                if ($dy < -1) {
                    $rope[$i][1]++;
                    $rope[$i][0] = $rope[$i - 1][0];
                }
            }
        }

        if (!in_array(implode(',', $rope[1]), $visitedPositionsPart1)) {
            array_push($visitedPositionsPart1, implode(',', $rope[1]));
        }
        if (!in_array(implode(',', $rope[9]), $visitedPositionsPart2)) {
            array_push($visitedPositionsPart2, implode(',', $rope[9]));
        }
        $count--;
    }
}

p('Part 1: ' . count($visitedPositionsPart1));
p('Part 2: ' . count($visitedPositionsPart2));
