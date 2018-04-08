<?php namespace SMBR;

use SMBR\Game;
use SMBR\World;
use SMBR\Level;

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

/*
 * TODO: do we need this?? the setVanilla stuff
 */
class World1 extends World {
    protected $name = "World 1";

    public function __construct(Game $game, int $num) {
        parent::__construct($game, $num);
    }
    
    public function setVanilla() {
        global $vanilla_level;
        $this->levels = [
            // TODO: only use 1,2,3,4 as key?
            '1-1' => new Level('1-1', $vanilla_level['1-1']->map, $vanilla_level['1-1']->enemy_data_offset, $vanilla_level['1-1']->level_data_offset, $this),
            '1-2' => new Level('1-2', $vanilla_level['1-2']->map, $vanilla_level['1-2']->enemy_data_offset, $vanilla_level['1-2']->level_data_offset, $this),
            '1-3' => new Level('1-3', $vanilla_level['1-3']->map, $vanilla_level['1-3']->enemy_data_offset, $vanilla_level['1-3']->level_data_offset, $this),
            '1-4' => new Level('1-4', $vanilla_level['1-4']->map, $vanilla_level['1-4']->enemy_data_offset, $vanilla_level['1-4']->level_data_offset, $this),
        ];
    }
}

class World2 extends World {
    protected $name = "World 2";

    public function __construct(Game $game, int $num) {
        parent::__construct($game, $num);
    }

    public function setVanilla() {
        global $vanilla_level;
        $this->levels = [
            '2-1' => new Level('2-1', $vanilla_level['2-1']->map, $vanilla_level['2-1']->enemy_data_offset, $vanilla_level['2-1']->level_data_offset, $this),
            '2-2' => new Level('2-2', $vanilla_level['2-2']->map, $vanilla_level['2-2']->enemy_data_offset, $vanilla_level['2-2']->level_data_offset, $this),
            '2-3' => new Level('2-3', $vanilla_level['2-3']->map, $vanilla_level['2-3']->enemy_data_offset, $vanilla_level['2-3']->level_data_offset, $this),
            '2-4' => new Level('2-4', $vanilla_level['2-4']->map, $vanilla_level['2-4']->enemy_data_offset, $vanilla_level['2-4']->level_data_offset, $this),
        ];
    }
}

class World3 extends World {
    protected $name = "World 3";

    public function __construct(Game $game, int $num) {
        parent::__construct($game, $num);
    }
    
    public function setVanilla() {
        global $vanilla_level;
        $this->levels = [
            '3-1' => new Level('3-1', $vanilla_level['3-1']->map, $vanilla_level['3-1']->enemy_data_offset, $vanilla_level['3-1']->level_data_offset, $this),
            '3-2' => new Level('3-2', $vanilla_level['3-2']->map, $vanilla_level['3-2']->enemy_data_offset, $vanilla_level['3-2']->level_data_offset, $this),
            '3-3' => new Level('3-3', $vanilla_level['3-3']->map, $vanilla_level['3-3']->enemy_data_offset, $vanilla_level['3-3']->level_data_offset, $this),
            '3-4' => new Level('3-4', $vanilla_level['3-4']->map, $vanilla_level['3-4']->enemy_data_offset, $vanilla_level['3-4']->level_data_offset, $this),
        ];
    }
}

class World4 extends World {
    protected $name = "World 4";

    public function __construct(Game $game, int $num) {
        parent::__construct($game, $num);
    }
    
    public function setVanilla() {
        global $vanilla_level;
        $this->levels = [
            '4-1' => new Level('4-1', $vanilla_level['4-1']->map, $vanilla_level['4-1']->enemy_data_offset, $vanilla_level['4-1']->level_data_offset, $this),
            '4-2' => new Level('4-2', $vanilla_level['4-2']->map, $vanilla_level['4-2']->enemy_data_offset, $vanilla_level['4-2']->level_data_offset, $this),
            '4-3' => new Level('4-3', $vanilla_level['4-3']->map, $vanilla_level['4-3']->enemy_data_offset, $vanilla_level['4-3']->level_data_offset, $this),
            '4-4' => new Level('4-4', $vanilla_level['4-4']->map, $vanilla_level['4-4']->enemy_data_offset, $vanilla_level['4-4']->level_data_offset, $this),
        ];
    }
}

class World5 extends World {
    protected $name = "World 5";

    public function __construct(Game $game, int $num) {
        parent::__construct($game, $num);
    }
    
    public function setVanilla() {
        global $vanilla_level;
        $this->levels = [
            '5-1' => new Level('5-1', $vanilla_level['5-1']->map, $vanilla_level['5-1']->enemy_data_offset, $vanilla_level['5-1']->level_data_offset, $this),
            '5-2' => new Level('5-2', $vanilla_level['5-2']->map, $vanilla_level['5-2']->enemy_data_offset, $vanilla_level['5-2']->level_data_offset, $this),
            '5-3' => new Level('5-3', $vanilla_level['5-3']->map, $vanilla_level['5-3']->enemy_data_offset, $vanilla_level['5-3']->level_data_offset, $this),
            '5-4' => new Level('5-4', $vanilla_level['5-4']->map, $vanilla_level['5-4']->enemy_data_offset, $vanilla_level['5-4']->level_data_offset, $this),
        ];
    }
}

class World6 extends World {
    protected $name = "World 6";

    public function __construct(Game $game, int $num) {
        parent::__construct($game, $num);
    }
    
    public function setVanilla() {
        global $vanilla_level;
        $this->levels = [
            '6-1' => new Level('6-1', $vanilla_level['6-1']->map, $vanilla_level['6-1']->enemy_data_offset, $vanilla_level['6-1']->level_data_offset, $this),
            '6-2' => new Level('6-2', $vanilla_level['6-2']->map, $vanilla_level['6-2']->enemy_data_offset, $vanilla_level['6-2']->level_data_offset, $this),
            '6-3' => new Level('6-3', $vanilla_level['6-3']->map, $vanilla_level['6-3']->enemy_data_offset, $vanilla_level['6-3']->level_data_offset, $this),
            '6-4' => new Level('6-4', $vanilla_level['6-4']->map, $vanilla_level['6-4']->enemy_data_offset, $vanilla_level['6-4']->level_data_offset, $this),
        ];
    }
}

class World7 extends World {
    protected $name = "World 7";

    public function __construct(Game $game, int $num) {
        parent::__construct($game, $num);
    }
    
    public function setVanilla() {
        global $vanilla_level;
        $this->levels = [
            '7-1' => new Level('7-1', $vanilla_level['7-1']->map, $vanilla_level['7-1']->enemy_data_offset, $vanilla_level['7-1']->level_data_offset, $this),
            '7-2' => new Level('7-2', $vanilla_level['7-2']->map, $vanilla_level['7-2']->enemy_data_offset, $vanilla_level['7-2']->level_data_offset, $this),
            '7-3' => new Level('7-3', $vanilla_level['7-3']->map, $vanilla_level['7-3']->enemy_data_offset, $vanilla_level['7-3']->level_data_offset, $this),
            '7-4' => new Level('7-4', $vanilla_level['7-4']->map, $vanilla_level['7-4']->enemy_data_offset, $vanilla_level['7-4']->level_data_offset, $this),
        ];
    }
}

class World8 extends World {
    protected $name = "World 8";

    public function __construct(Game $game, int $num) {
        parent::__construct($game, $num);
    }
    
    public function setVanilla() {
        global $vanilla_level;
        $this->levels = [
            '8-1' => new Level('8-1', $vanilla_level['8-1']->map, $vanilla_level['8-1']->enemy_data_offset, $vanilla_level['8-1']->level_data_offset, $this),
            '8-2' => new Level('8-2', $vanilla_level['8-2']->map, $vanilla_level['8-2']->enemy_data_offset, $vanilla_level['8-2']->level_data_offset, $this),
            '8-3' => new Level('8-3', $vanilla_level['8-3']->map, $vanilla_level['8-3']->enemy_data_offset, $vanilla_level['8-3']->level_data_offset, $this),
            '8-4' => new Level('8-4', $vanilla_level['8-4']->map, $vanilla_level['8-4']->enemy_data_offset, $vanilla_level['8-4']->level_data_offset, $this),
        ];
    }
}
