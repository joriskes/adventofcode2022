<?php
require __DIR__ . '/../bootstrap.php';

$input = file_get_contents(__DIR__ . '/input.txt');

class Valve
{
    private $label;
    private $rate;
    private $linkLabels;
    private $links;

    public function __construct($label, $rate, $links)
    {
        $this->label = $label;
        $this->rate = $rate;
        $this->linkLabels = $links;
        $this->links = [];
    }

    public function __toString(): string
    {
        return 'V ' . $this->label . ' (' . $this->rate . ') -> ' . implode(',', $this->linkLabels);
    }

    public function linkUp($valves)
    {
        $this->links = [];
        foreach ($this->linkLabels as $l) {
            $this->links[$l] = $valves[$l];
        }
    }

    // Simulates moves until time is left, starting on this
    // Returns the predicted score and steps taken
    public function simulateStep($timeLeft, $valvesWithNonZeroRate, $steps = [], $openedValves = [])
    {
        if ($timeLeft < 1 || count($openedValves) === $valvesWithNonZeroRate) {
            return [0, $steps];
        }

        if (!isset($openedValves[$this->label]) && $this->rate > 0) {
            // Simulate opening this, make that the baseline score to compare
            $maxScore = $this->rate * $timeLeft;
            [$s, $chosenMoves] = $this->simulateStep($timeLeft - 1, $valvesWithNonZeroRate, [...$steps, $this->label], [...$openedValves, $this->label => $this->rate * $timeLeft]);
            $maxScore += $s;
        } else {
            $chosenMoves = [...$steps, $this->label];
            $maxScore = 0;
        }

        // Simulate moving options
        foreach ($this->linkLabels as $l) {

            $skip = false;
            // We can skip a step if we are moving from A to B and back to A
            if (count($steps) > 1) {
                if ($steps[count($steps) - 1] == $l) {
                    $skip = true;
                }
            }

            if (!$skip) {
                [$score, $newSteps] = $this->links[$l]->simulateStep($timeLeft - 1, $valvesWithNonZeroRate, [...$steps, $this->label], $openedValves);
                if ($score > $maxScore) {
                    $chosenMoves = $newSteps;
                    $maxScore = $score;
                }
            }
        }
        return [$maxScore, $chosenMoves];
    }
}

$lines = input_to_lines($input);
$valves = [];
$startValve = null;
$valvesWithNonZeroRate = 0;
foreach ($lines as $line) {
    preg_match_all('/Valve (\w\w) has flow rate=(\d+); tunnels? leads? to valves? (((\w\w)+,?\s?)+)/i', $line, $matches);
    $label = $matches[1][0];
    $rate = intval($matches[2][0]);
    $links = explode(', ', $matches[3][0]);
    $valves[$label] = new Valve($label, $rate, $links);
    if ($rate > 0) $valvesWithNonZeroRate++;
}

foreach ($valves as $valve) {
    $valve->linkUp($valves);
}

$timeLeft = 29;
[$score, $chosenMoves] = $valves['AA']->simulateStep($timeLeft, $valvesWithNonZeroRate);

//p('Chosen moves: ' . implode(',', $chosenMoves));
$part1 = $score;

p('Part 1: ' . $part1);



//p('Part 2: ' . $part2);
