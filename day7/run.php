<?php
require __DIR__ . '/../bootstrap.php';

$input = file_get_contents(__DIR__ . '/input.txt');
$lines = input_to_lines($input);

class AOCFile {
    private $name;
    private $size;

    public function __construct($name, $size)
    {
        $this->name = $name;
        $this->size = $size;
    }

    /**
     * @return int
     */
    public function getSize(): int
    {
        return $this->size;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }
}

class AOCDirectory {
    private AOCDirectory | null $parent = null;
    private $name = '';
    private $files = [];
    private $directories = [];

    public function __construct($name, $parent = null)
    {
        $this->parent = $parent;
        $this->name = $name;
    }

    public function addContent($contentLine) {
        [$size, $name] = explode(' ',$contentLine);

        if(is_numeric($size)) {
            $this->files[] = new AOCFile($name, $size);
        } else {
            if($size == 'dir') {
                $this->directories[] = new AOCDirectory($name, $this);
            }
        }
    }

    public function getDirectory($name) {
        foreach ($this->directories as $c) {
            if($c->getName() === $name) {
                return $c;
            }
        }
        p('Entered non existing directory');
        die();
    }

    public function getSize() {
        $sum = 0;
        foreach ($this->files as $file) {
            $sum += $file->getSize();
        }
        foreach ($this->directories as $dir) {
            $sum += $dir->getSize();
        }
        return $sum;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return AOCDirectory|mixed|null
     */
    public function getParent(): mixed
    {
        return $this->parent;
    }

    public function getSizeRestricted($restriction, $carriedResult = 0) {
        $sum = 0;
        foreach ($this->files as $file) {
            $sum += $file->getSize();
        }
        foreach ($this->directories as $dir) {
            [$s, $carriedResult] = $dir->getSizeRestricted($restriction, $carriedResult);
            $sum += $s;
        }
        if($sum < $restriction) $carriedResult+=$sum;
        return [$sum, $carriedResult];
    }

    public function getSizeOfDirectoryClosestToRequired($required, $currentClosest = 9999999999999) {
        $sum = 0;
        foreach ($this->files as $file) {
            $sum += $file->getSize();
        }
        foreach ($this->directories as $dir) {
            [$s, $currentClosest] = $dir->getSizeOfDirectoryClosestToRequired($required, $currentClosest);
            $sum += $s;
        }
        if($sum > $required && $sum < $currentClosest) {
            $currentClosest = $sum;
        }

        return [$sum, $currentClosest];
    }
}


$currentDirectory = new AOCDirectory('');
$rootDirectory = $currentDirectory;

$index = 1; // Skip first line, we know it's cd /
while($index < count($lines)) {
    if($lines[$index][0] === '$') {
        $cmd = substr($lines[$index], 2);
        if($cmd === 'ls') {
            // Read ls result lines until a new commmand
            $index++;
            while($index < count($lines) && $lines[$index][0] !== '$') {
                $currentDirectory->addContent($lines[$index]);
                $index++;
            }
        } else {
            $cmdAr = explode(' ', $cmd);
            if($cmdAr[0] == 'cd') {
                if($cmdAr[1] == '..') {
                    $currentDirectory = $currentDirectory->getParent();
                } else {
                    $currentDirectory = $currentDirectory->getDirectory($cmdAr[1]);
                }
            } else {
                p('Command at index '.$index.' not parsed?'. $lines[$index]);
            }
            $index++;
        }
    } else {
        p('Line at index '.$index.' not parsed?'. $lines[$index]);
        $index++;
    }
}

[$s, $part1] = $rootDirectory->getSizeRestricted(100000);
p('Part 1: ' . $part1);

$diskSpace = 70000000;
$available = $diskSpace - $rootDirectory->getSize();
$needed = 30000000;
$required = $needed - $available;

[$s, $part2] = $rootDirectory->getSizeOfDirectoryClosestToRequired($required);
p('Part 2: ' . $part2);
