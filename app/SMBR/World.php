<?php namespace SMBR;

use SMBR\Game;
use SMBR\Level;
use SMBR\VanillaLevels;
use SMBR\World;

class World
{
    protected $name = 'Unknown';
    protected $game;
    public $num = 0;
    public $levels = [];
    protected $num_levels; // probably not needed, can use count()
    protected $vanilla;

    public function __construct(Game $game, int $num)
    {
        $this->game = $game;
        $this->num = $num;
        $this->levelindex = 0;
        $this->vanilla = new VanillaLevels();
    }

    public function getLevels()
    {
        return $this->levels;
    }

    public function getName()
    {
        return $this->name;
    }
}

class World1 extends World
{
    protected $name = "World 1";

    public function __construct(Game $game, int $num)
    {
        parent::__construct($game, $num);
    }

    public function setVanilla()
    {
        global $vanilla_level;
        $this->levels = [
            // TODO: only use 1,2,3,4 as key?
            '1-1' => $this->vanilla->level['1-1'],
            '1-2 Pipe Transition' => $this->vanilla->level['Pipe Transition'],
            '1-2' => $this->vanilla->level['1-2'],
            '1-3' => $this->vanilla->level['1-3'],
            '1-4' => $this->vanilla->level['1-4'],
        ];
    }
}

class World1NoPipeTransition extends World
{
    protected $name = "World 1";

    public function __construct(Game $game, int $num)
    {
        parent::__construct($game, $num);
    }

    public function setVanilla()
    {
        global $vanilla_level;
        $this->levels = [
            // TODO: only use 1,2,3,4 as key?
            '1-1' => $this->vanilla->level['1-1'],
            '1-2' => $this->vanilla->level['1-2'],
            '1-3' => $this->vanilla->level['1-3'],
            '1-4' => $this->vanilla->level['1-4'],
        ];
    }
}

class World2 extends World
{
    protected $name = "World 2";

    public function __construct(Game $game, int $num)
    {
        parent::__construct($game, $num);
    }

    public function setVanilla()
    {
        global $vanilla_level;
        $this->levels = [
            '2-1' => $this->vanilla->level['2-1'],
            '2-1 Pipe Transition' => $this->vanilla->level['Pipe Transition'],
            '2-2' => $this->vanilla->level['2-2'],
            '2-3' => $this->vanilla->level['2-3'],
            '2-4' => $this->vanilla->level['2-4'],
        ];
    }
}

class World2NoPipeTransition extends World
{
    protected $name = "World 2";

    public function __construct(Game $game, int $num)
    {
        parent::__construct($game, $num);
    }

    public function setVanilla()
    {
        global $vanilla_level;
        $this->levels = [
            '2-1' => $this->vanilla->level['2-1'],
            '2-2' => $this->vanilla->level['2-2'],
            '2-3' => $this->vanilla->level['2-3'],
            '2-4' => $this->vanilla->level['2-4'],
        ];
    }
}

class World3 extends World
{
    protected $name = "World 3";

    public function __construct(Game $game, int $num)
    {
        parent::__construct($game, $num);
    }

    public function setVanilla()
    {
        global $vanilla_level;
        $this->levels = [
            '3-1' => $this->vanilla->level['3-1'],
            '3-2' => $this->vanilla->level['3-2'],
            '3-3' => $this->vanilla->level['3-3'],
            '3-4' => $this->vanilla->level['3-4'],
        ];
    }
}

class World4 extends World
{
    protected $name = "World 4";

    public function __construct(Game $game, int $num)
    {
        parent::__construct($game, $num);
    }

    public function setVanilla()
    {
        global $vanilla_level;
        $this->levels = [
            '4-1' => $this->vanilla->level['4-1'],
            '4-1 Pipe Transition' => $this->vanilla->level['Pipe Transition'],
            '4-2' => $this->vanilla->level['4-2'],
            '4-3' => $this->vanilla->level['4-3'],
            '4-4' => $this->vanilla->level['4-4'],
        ];
    }
}

class World4NoPipeTransition extends World
{
    protected $name = "World 4";

    public function __construct(Game $game, int $num)
    {
        parent::__construct($game, $num);
    }

    public function setVanilla()
    {
        global $vanilla_level;
        $this->levels = [
            '4-1' => $this->vanilla->level['4-1'],
            '4-2' => $this->vanilla->level['4-2'],
            '4-3' => $this->vanilla->level['4-3'],
            '4-4' => $this->vanilla->level['4-4'],
        ];
    }
}

class World5 extends World
{
    protected $name = "World 5";

    public function __construct(Game $game, int $num)
    {
        parent::__construct($game, $num);
    }

    public function setVanilla()
    {
        global $vanilla_level;
        $this->levels = [
            '5-1' => $this->vanilla->level['5-1'],
            '5-2' => $this->vanilla->level['5-2'],
            '5-3' => $this->vanilla->level['5-3'],
            '5-4' => $this->vanilla->level['5-4'],
        ];
    }
}

class World6 extends World
{
    protected $name = "World 6";

    public function __construct(Game $game, int $num)
    {
        parent::__construct($game, $num);
    }

    public function setVanilla()
    {
        global $vanilla_level;
        $this->levels = [
            '6-1' => $this->vanilla->level['6-1'],
            '6-2' => $this->vanilla->level['6-2'],
            '6-3' => $this->vanilla->level['6-3'],
            '6-4' => $this->vanilla->level['6-4'],
        ];
    }
}

class World7 extends World
{
    protected $name = "World 7";

    public function __construct(Game $game, int $num)
    {
        parent::__construct($game, $num);
    }

    public function setVanilla()
    {
        global $vanilla_level;
        $this->levels = [
            '7-1' => $this->vanilla->level['7-1'],
            '7-2' => $this->vanilla->level['7-2'],
            '7-3' => $this->vanilla->level['7-3'],
            '7-4' => $this->vanilla->level['7-4'],
        ];
    }
}

class World8 extends World
{
    protected $name = "World 8";

    public function __construct(Game $game, int $num)
    {
        parent::__construct($game, $num);
    }

    public function setVanilla()
    {
        global $vanilla_level;
        $this->levels = [
            '8-1' => $this->vanilla->level['8-1'],
            '8-2' => $this->vanilla->level['8-2'],
            '8-3' => $this->vanilla->level['8-3'],
            '8-4' => $this->vanilla->level['8-4'],
        ];
    }
}
