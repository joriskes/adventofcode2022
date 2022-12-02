<?php
require __DIR__ . '/../bootstrap.php';

$input = file_get_contents(__DIR__ . '/input.txt');
// $input = 'C Y';
$lines = input_to_lines($input);

define('ROCK', 'rock');
define('PAPER', 'paper');
define('SCISSORS', 'scissors');

// Returns 0 for loss, 1 for draw and 2 for win
function RPSResult($mine, $theirs) {
    if($mine === $theirs) {
        return 1;
    }
    if($mine === ROCK && $theirs === SCISSORS) {
        return 2;
    }
    if($mine === PAPER && $theirs === ROCK) {
        return 2;
    }
    if($mine === SCISSORS && $theirs === PAPER) {
        return 2;
    }

    return 0;
}

function RPSScore($mine, $theirs) {
    $score = 0;
    switch ($mine) {
        case ROCK:
            $score+=1;
            break;
        case PAPER:
            $score+=2;
            break;
        case SCISSORS:
            $score+=3;
            break;
    }
    $score+=RPSResult($mine, $theirs) * 3;
    return $score;
}

$rpsLookup = [
    'A' => ROCK,
    'B' => PAPER,
    'C' => SCISSORS,
    'X' => ROCK,
    'Y' => PAPER,
    'Z' => SCISSORS
];

$part1 = 0;
$part2 = 0;

foreach ($lines as $l) {
    $split = explode(' ', $l);
    if(count($split) == 2) {
        $theirs = $rpsLookup[$split[0]];
        $mine = $rpsLookup[$split[1]];
        $part1+= RPSScore($mine, $theirs);

        switch ($split[1]) {
            case 'X': // lose
                if($theirs === ROCK) $mine = SCISSORS;
                if($theirs === PAPER) $mine = ROCK;
                if($theirs === SCISSORS) $mine = PAPER;
                break;
            case 'Y': // draw
                $mine = $theirs;
                break;
            case 'Z': // win
                if($theirs === ROCK) $mine = PAPER;
                if($theirs === PAPER) $mine = SCISSORS;
                if($theirs === SCISSORS) $mine = ROCK;
                break;
        }
        $part2+= RPSScore($mine, $theirs);
    }
}

p('Part 1: ' . $part1);
p('Part 2: ' . $part2);
