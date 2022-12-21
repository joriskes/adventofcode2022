<?php
require __DIR__ . '/../bootstrap.php';

$input = file_get_contents(__DIR__ . '/input.txt');


class Valve
{
    public $label;
    public $rate;
    public $links;

    public function __construct($label, $rate, $links)
    {
        $this->label = $label;
        $this->rate = $rate;
        $this->links = $links;
    }
}

class Route
{
    public $unopenedValves;
    public $time;
    public $position;
    public $totalPressure;

    public function __construct($unopenedValves, $time = 30)
    {
        $this->unopenedValves = $unopenedValves;
        $this->time = $time;
        $this->position = 'AA';
        $this->totalPressure = 0;
    }

    // Steps the distance and opens the valve (if needed)
    public function step(Valve $valve, $distances)
    {
        $this->time -= $distances[$this->position][$valve->label] + 1;
        if ($this->time < 1) {
            return false;
        }
        unset($this->unopenedValves[$valve->label]);
        $this->position = $valve->label;
        $this->totalPressure += $valve->rate * $this->time;
        return true;
    }
}

function getPressuredValves($valves)
{
    $openValves = [];
    foreach ($valves as $key => $valve) {
        if ($valve->rate > 0) {
            $openValves[$key] = false;
        }
    }
    return $openValves;
}

// Return all permutations of the valves list split into two parts
function splitTodo($todo, $valves, $result = [])
{
    $next = array_shift($valves);
    // We need a split with at least 30% of valves on either side
    $minimumValvesNeeded = floor(count($valves) / 3);
    foreach ($todo as $k => $t) {
        $t[] = $next;
        $split = $todo;
        $split[$k] = $t;
        if ($valves) {
            $result = splitTodo($split, $valves, $result);
        } elseif (count($split[0]) > $minimumValvesNeeded && count($split[1]) > $minimumValvesNeeded) {
            $result[] = $split;
        }
    }
    return $result;
}

// Calculate distances to all valves from every valve
function calculateDistances($valves)
{
    $distances = [];
    foreach ($valves as $valve) {
        $targets = [];
        $routes = array_map(function ($v) {
            return [$v];
        }, $valve->links);
        while ($routes) {
            $newRoutes = [];
            foreach ($routes as $route) {
                $targetValve = $route[count($route) - 1];
                $targets[$targetValve] = count($route);
                foreach ($valves[$targetValve]->links as $connection) {
                    if (!isset($targets[$connection]) && $connection !== $valve->label) {
                        $newRoute = $route;
                        $newRoute[] = $connection;
                        $newRoutes[] = $newRoute;
                    }
                }
            }
            $routes = $newRoutes;
        }
        $distances[$valve->label] = $targets;
    }
    return $distances;
}

// Run each possible route and calculate pressure, return max over all routes
function calculatePressure($valves, Route $route, $distances)
{
    $routes = [$route];
    $max = 0;
    while ($routes) {
        $newRoutes = [];
        foreach ($routes as $route) {
            foreach ($route->unopenedValves as $valve => $tmp) {
                $newRoute = (clone $route);
                if ($newRoute->step($valves[$valve], $distances)) {
                    $newRoutes[] = $newRoute;
                    $max = max($max, $newRoute->totalPressure);
                }
            }
        }
        $routes = $newRoutes;
    }
    return $max;
}

$lines = input_to_lines($input);
$valves = [];
foreach ($lines as $line) {
    if (preg_match('/^Valve ([A-Z]+) has flow rate=([0-9]+); tunnels? leads? to valves? (.*)$/i', $line, $matches)) {
        $valves[$matches[1]] = new Valve($matches[1], $matches[2], explode(', ', $matches[3]));
    }
}
// Precalculate distances between all nodes
$distances = calculateDistances($valves);

$unopenedValves = getPressuredValves($valves);
$part1 = calculatePressure($valves, new Route($unopenedValves), $distances);
p('Part 1: ' . $part1);

$unopenedValves = array_keys(getPressuredValves($valves));
$todo = splitTodo([array_splice($unopenedValves, 0, 1), []], $unopenedValves);

$max = 0;
foreach ($todo as $i => list($p1Valves, $p2Valves)) {
    $max = max($max,
        calculatePressure($valves, new Route(array_flip($p1Valves), 26), $distances)
        + calculatePressure($valves, new Route(array_flip($p2Valves), 26), $distances)
    );
}
$part2 = $max;
p('Part 2: ' . $part2);
