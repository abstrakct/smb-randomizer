<?php namespace SMBR;

use SMBR\Level;
use SMBR\World;

class World
{
    protected $name = 'Unknown';
    public $num = 0;
    public $levels = [];
    protected $num_levels; // probably not needed, can use count()

    public function __construct(int $num)
    {
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

    public function hasLevel($name)
    {
        foreach ($this->levels as $level) {
            if ($level->name == $name) {
                return true;
            }
        }

        return false;
    }
}

class World1 extends World
{
    protected $name = "World 1";

    public function __construct(int $num)
    {
        parent::__construct($num);
    }

    public function setVanilla()
    {
        $this->levels = [
            Level::get('1-1'),
            Level::get('Pipe Transition'),
            Level::get('1-2'),
            Level::get('1-3'),
            Level::get('1-4'),
        ];
    }
}

class World1NoPipeTransition extends World
{
    protected $name = "World 1";

    public function __construct(int $num)
    {
        parent::__construct($num);
    }

    public function setVanilla()
    {
        $this->levels = [
            Level::get('1-1'),
            Level::get('1-2'),
            Level::get('1-3'),
            Level::get('1-4'),
        ];
    }
}

class World2 extends World
{
    protected $name = "World 2";

    public function __construct(int $num)
    {
        parent::__construct($num);
    }

    public function setVanilla()
    {
        $this->levels = [
            Level::get('2-1'),
            Level::get('Pipe Transition'),
            Level::get('2-2'),
            Level::get('2-3'),
            Level::get('2-4'),
        ];
    }
}

class World2NoPipeTransition extends World
{
    protected $name = "World 2";

    public function __construct(int $num)
    {
        parent::__construct($num);
    }

    public function setVanilla()
    {
        $this->levels = [
            Level::get('2-1'),
            Level::get('2-2'),
            Level::get('2-3'),
            Level::get('2-4'),
        ];
    }
}

class World3 extends World
{
    protected $name = "World 3";

    public function __construct(int $num)
    {
        parent::__construct($num);
    }

    public function setVanilla()
    {
        $this->levels = [
            Level::get('3-1'),
            Level::get('3-2'),
            Level::get('3-3'),
            Level::get('3-4'),
        ];
    }
}

class World4 extends World
{
    protected $name = "World 4";

    public function __construct(int $num)
    {
        parent::__construct($num);
    }

    public function setVanilla()
    {
        $this->levels = [
            Level::get('4-1'),
            Level::get('Pipe Transition'),
            Level::get('4-2'),
            Level::get('4-3'),
            Level::get('4-4'),
        ];
    }
}

class World4NoPipeTransition extends World
{
    protected $name = "World 4";

    public function __construct(int $num)
    {
        parent::__construct($num);
    }

    public function setVanilla()
    {
        $this->levels = [
            Level::get('4-1'),
            Level::get('4-2'),
            Level::get('4-3'),
            Level::get('4-4'),
        ];
    }
}

class World5 extends World
{
    protected $name = "World 5";

    public function __construct(int $num)
    {
        parent::__construct($num);
    }

    public function setVanilla()
    {
        $this->levels = [
            Level::get('5-1'),
            Level::get('5-2'),
            Level::get('5-3'),
            Level::get('5-4'),
        ];
    }
}

class World6 extends World
{
    protected $name = "World 6";

    public function __construct(int $num)
    {
        parent::__construct($num);
    }

    public function setVanilla()
    {
        $this->levels = [
            Level::get('6-1'),
            Level::get('6-2'),
            Level::get('6-3'),
            Level::get('6-4'),
        ];
    }
}

class World7 extends World
{
    protected $name = "World 7";

    public function __construct(int $num)
    {
        parent::__construct($num);
    }

    public function setVanilla()
    {
        $this->levels = [
            Level::get('7-1'),
            Level::get('7-2'),
            Level::get('7-3'),
            Level::get('7-4'),
        ];
    }
}

class World8 extends World
{
    protected $name = "World 8";

    public function __construct(int $num)
    {
        parent::__construct($num);
    }

    public function setVanilla()
    {
        $this->levels = [
            Level::get('8-1'),
            Level::get('8-2'),
            Level::get('8-3'),
            Level::get('8-4'),
        ];
    }
}
