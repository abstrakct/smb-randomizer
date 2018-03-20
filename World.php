<?php namespace SMBR;

require_once "World/Worlds.php";

class World {
    protected $name = 'Unknown';
    protected $game;
    protected $num;
    protected $levels = [];
    protected $num_levels;

    public function __construct(Game $game, int $num) {
        $this->game = $game;
        $this->num = $num;
    }

    public function getLevels() {
        return $this->levels;
    }
}
