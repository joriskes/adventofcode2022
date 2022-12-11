<?php
require __DIR__ . '/../bootstrap.php';

$input = file_get_contents(__DIR__ . '/input.txt');
$lines = input_to_lines($input);

class Monkey
{
    private $id;
    private $items = [];
    private $operation;
    private $test;
    private $trueId;
    private $falseId;
    private $inspections;

    public function parse($lines)
    {
        $this->id = intval(str_replace('Monkey ', '', str_replace(':', '', $lines[0])));
        $this->items = array_map('intval', explode(', ', str_replace('Starting items: ', '', $lines[1])));
        $this->operation = str_replace('Operation: ', '', $lines[2]);
        $this->test = intval(str_replace('Test: divisible by ', '', $lines[3]));
        $this->trueId = intval(str_replace('If true: throw to monkey ', '', $lines[4]));
        $this->falseId = intval(str_replace('If false: throw to monkey ', '', $lines[5]));
        $this->inspections = 0;
    }

    public function __toString(): string
    {
        return 'Monkey ' . $this->id . ': ' . implode(',', $this->items) . ' I:' . $this->inspections;
    }

    public function runRound($monkeys, $part2 = false)
    {
        while (count($this->items) > 0) {
            $this->inspections++;
            $itemLevel = array_shift($this->items);
            $level = $this->runOperation($itemLevel, $part2);
            if ($part2) {
                // Part 2: to prevent huge numbers, we mod the result with the
                // product of all mods
                $sumTest = 1;
                foreach ($monkeys as $monkey) {
                    $sumTest *= $monkey->getTest();
                }
                $level = $level % $sumTest;
            }
            if ($level % $this->test === 0) {
                $monkeys[$this->trueId]->catchItem($level);
            } else {
                $monkeys[$this->falseId]->catchItem($level);
            }
        }
    }

    private function runOperation($itemLevel, $part2 = false)
    {
        $op = str_replace('new = ', '', $this->operation);
        $op = str_replace('old', $itemLevel, $op);
        $res = 0;
        eval('$res = ' . $op . ';');
        if ($part2) {
            return $res;
        }
        return floor($res / 3);
    }

    public function catchItem($itemLevel)
    {
        $this->items[] = $itemLevel;
    }

    /**
     * @return mixed
     */
    public function getInspections()
    {
        return $this->inspections;
    }

    /**
     * @return mixed
     */
    public function getTest()
    {
        return $this->test;
    }
}

class Monkeys
{
    /**
     * @param Monkey[]
     */
    private $list;

    public function __construct()
    {
        $this->list = [];
    }

    public function addMonkey(Monkey $m)
    {
        array_push($this->list, $m);
    }

    public function runRounds($numberOfRounds, $part2 = false)
    {
        $rounds = 0;
        while ($rounds < $numberOfRounds) {
            foreach ($this->list as $monkey) {
                $monkey->runRound($this->list, $part2);
            }
            $rounds++;
        }
    }

    public function getSolution()
    {
        $res = [];
        foreach ($this->list as $monkey) {
            array_push($res, $monkey->getInspections());
        }
        sort($res);
        return array_pop($res) * array_pop($res);
    }

}


$monkeysPart1 = new Monkeys();
$monkeysPart2 = new Monkeys();

$index = 0;
while ($index < count($lines)) {
    $m = new Monkey();
    $m->parse(array_slice($lines, $index, 6));
    $monkeysPart1->addMonkey(clone $m);
    $monkeysPart2->addMonkey(clone $m);
    $index += 7;
}

$monkeysPart1->runRounds(20);
$part1 = $monkeysPart1->getSolution();
p('Part 1: ' . $part1);

$monkeysPart2->runRounds(10000, true);
$part2 = $monkeysPart2->getSolution();
p('Part 2: ' . $part2);
