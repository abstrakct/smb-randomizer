<?php namespace SMBR;

use SMBR\Game;
use SMBR\Level;
use SMBR\World;

class World
{
    protected $name = 'Unknown';
    protected $game;
    public $num = 0;
    public $levels = [];
    protected $num_levels; // probably not needed, can use count()

    public function __construct(Game $game, int $num)
    {
        $this->game = $game;
        $this->num = $num;
        $this->levelindex = 0;
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
        $this->levels = [
            // TODO: only use 1,2,3,4 as key?
            '1-1' => Level::get('1-1'),
            '1-2 Pipe Transition' => Level::get('Pipe Transition'),
            '1-2' => Level::get('1-2'),
            '1-3' => Level::get('1-3'),
            '1-4' => Level::get('1-4'),
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
        $this->levels = [
            // TODO: only use 1,2,3,4 as key?
            '1-1' => Level::get('1-1'),
            '1-2' => Level::get('1-2'),
            '1-3' => Level::get('1-3'),
            '1-4' => Level::get('1-4'),
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
        $this->levels = [
            '2-1' => Level::get('2-1'),
            '2-1 Pipe Transition' => Level::get('Pipe Transition'),
            '2-2' => Level::get('2-2'),
            '2-3' => Level::get('2-3'),
            '2-4' => Level::get('2-4'),
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
        $this->levels = [
            '2-1' => Level::get('2-1'),
            '2-2' => Level::get('2-2'),
            '2-3' => Level::get('2-3'),
            '2-4' => Level::get('2-4'),
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
        $this->levels = [
            '3-1' => Level::get('3-1'),
            '3-2' => Level::get('3-2'),
            '3-3' => Level::get('3-3'),
            '3-4' => Level::get('3-4'),
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
        $this->levels = [
            '4-1' => Level::get('4-1'),
            '4-1 Pipe Transition' => Level::get('Pipe Transition'),
            '4-2' => Level::get('4-2'),
            '4-3' => Level::get('4-3'),
            '4-4' => Level::get('4-4'),
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
        $this->levels = [
            '4-1' => Level::get('4-1'),
            '4-2' => Level::get('4-2'),
            '4-3' => Level::get('4-3'),
            '4-4' => Level::get('4-4'),
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
        $this->levels = [
            '5-1' => Level::get('5-1'),
            '5-2' => Level::get('5-2'),
            '5-3' => Level::get('5-3'),
            '5-4' => Level::get('5-4'),
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
        $this->levels = [
            '6-1' => Level::get('6-1'),
            '6-2' => Level::get('6-2'),
            '6-3' => Level::get('6-3'),
            '6-4' => Level::get('6-4'),
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
        $this->levels = [
            '7-1' => Level::get('7-1'),
            '7-2' => Level::get('7-2'),
            '7-3' => Level::get('7-3'),
            '7-4' => Level::get('7-4'),
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
        $this->levels = [
            '8-1' => Level::get('8-1'),
            '8-2' => Level::get('8-2'),
            '8-3' => Level::get('8-3'),
            '8-4' => Level::get('8-4'),
        ];
    }
}
