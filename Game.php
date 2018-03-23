<?php namespace SMBR;

require_once "World.php";
require_once "Level.php";

class Game {
    public $worlds = [];

    public function __construct() {
        $this->worlds = [
            '1' => new World\World1($this, 1),
            '2' => new World\World2($this, 2),
            '3' => new World\World3($this, 3),
            '4' => new World\World4($this, 4),
            '5' => new World\World5($this, 5),
            '6' => new World\World6($this, 6),
            '7' => new World\World7($this, 7),
            '8' => new World\World8($this, 8),
        ];
    }

    public function setVanilla() {
        foreach ($this->worlds as $world) {
            $world->setVanilla();
        }
    }
}
