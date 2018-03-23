<?php namespace SMBR;

require_once "World/Worlds.php";

class World {
    protected $name = 'Unknown';
    protected $game;
    public $num;
    public $levels = [];
    protected $num_levels; // probably not needed, can use count()

    public function __construct(Game $game, int $num) {
        $this->game = $game;
        $this->num = $num;
        $this->levelindex = 0;
    }

    public function getLevels() {
        return $this->levels;
    }

}
