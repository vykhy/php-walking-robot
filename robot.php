<?php

class Robot {
    private int $x;
    private int $y;
    private string $direction;
    private array $path;

    private static array $directions = ['north', 'south', 'east', 'west'];


    public function __construct(int $x, int $y, string $direction, string $path)
    {
        if(!in_array($direction, self::$directions)){
            throw new Exception('Invalid direction. Direction must be north, south, east or west');
        }
        if(!is_numeric($x) or !is_numeric($y)){
            throw new Exception('Invalid co-ordinates. Co-ordinates x and y must be negative or positive integers');
        }

        $this->x = $x;
        $this->y = $y;
        $this->direction = $direction;        
        $this->path = str_split($path);
    }

    private function turnRight(): void
    {
        switch($this->direction){
            case 'south':
                $this->direction = 'west';
                break;
            case 'west':
                $this->direction = 'north';
                break;
            case 'north':
                $this->direction = 'east';
                break;
            case 'east':
                $this->direction = 'south';
                break;
        }
    }

    private function turnLeft(): void
    {
        switch($this->direction){
            case 'south':
                $this->direction = 'east';
                break;
            case 'east':
                $this->direction = 'north';
                break;
            case 'north':
                $this->direction = 'west';
                break;
            case 'west':
                $this->direction = 'south';
                break;
        }
    }

    private function move(int $steps): void
    {
        switch($this->direction){
            case 'south':
                $this->y -= $steps;
                break;
            case 'west':
                $this->x -= $steps;
                break;
            case 'north':
                $this->y += $steps;
                break;
            case 'east':
                $this->x += $steps;
                break;
        }
    }

    private function getSteps(int $index): array 
    {
        // steps could be more than 1 digit. This method finds the string of numbers following 'w' and
        // returns it as steps
        // also returns the number of digits as those operations can be skipped (they are not l, r, or w commands)
        $steps = '';
        $i = $index;
        while($i+1 < count($this->path) and is_numeric($this->path[$i+1])){
            $steps = $steps . $this->path[$i+1];
            $i += 1;
        }
        return ['steps' => (int)$steps, 'skip' => $i - $index];
    }

    public function walk(): void
    {
        for($i = 0; $i < count($this->path); $i += 1){
            switch($this->path[$i]){
                case 'l':
                    $this->turnLeft();
                    break;
                case 'r':
                    $this->turnRight();
                    break;
                case 'w':
                    if($i + 1 > count($this->path) - 1
                    or !is_numeric($this->path[$i + 1])){
                        throw new Exception('Invalid input. Operation \'W\' must be followed by a positive integer');
                    }
                    $stepsDetails = $this->getSteps($i);

                    $steps = $stepsDetails['steps'];
                    $stepsToSkip = $stepsDetails['skip'];

                    $this->move($steps);
                    $i += $stepsToSkip;
                    break;
                // we simply skip over if invalid input exists
                // optionally we can raise an exception
            }
        }
        echo($this->x . ' ' . $this->y . ' ' .  ucfirst($this->direction));
    }
}


function runScript(array $argv): void
{        
    try{
        if(count($argv) < 4){
            // path may be empty. In that case, we will simply not move the robot
            throw new Exception('Invalid input. Provide arguments x, y, initial direction and path');
        }
        $x = $argv[1];
        $y = $argv[2];
        $direction = strtolower($argv[3]);
        $path = strtolower($argv[4] ?? '');

        $diggy = new Robot($x, $y, $direction, $path);
        $diggy->walk();
    }
    catch(Exception $e){
        echo $e->getMessage();
    }
}

runScript($argv);