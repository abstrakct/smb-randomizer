<?php namespace SMBR;

require_once "World.php";
require_once "Level.php";

class Game {
    protected $worlds = [];

    public function __construct() {
        $this->worlds = [
            'World 1' => new World\World1($this, 1),
            'World 2' => new World\World2($this, 2),
            'World 3' => new World\World3($this, 3),
            'World 4' => new World\World4($this, 4),
            'World 5' => new World\World5($this, 5),
            'World 6' => new World\World6($this, 6),
            'World 7' => new World\World7($this, 7),
            'World 8' => new World\World8($this, 8),
        ];
    }

    public function structtest() {
        foreach ($this->worlds as $world) {
            foreach ($world->getLevels() as $level) {
                print("LEVEL: " . $level->getName() . "\n");
            }
        }
    }
}
