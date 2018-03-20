<?php namespace SMBR\World;

use SMBR\Game;
use SMBR\World;
use SMBR\Level;

class World1 extends World {
    protected $name = "World 1";

    public function __construct(Game $game, int $num) {
        parent::__construct($game, $num);
        $this->levels = [
            '1-1' => new Level('1-1', 0x25, 0x1f11, $this),
            '1-2' => new Level('1-2', 0xc0, 0x20e8, $this),
            '1-3' => new Level('1-3', 0x26, 0x1f2f, $this),
            '1-4' => new Level('1-4', 0x60, 0x1d80, $this)
        ];
    }
}

class World2 extends World {
    protected $name = "World 2";

    public function __construct(Game $game, int $num) {
        parent::__construct($game, $num);
        $this->levels = [
            '2-1' => new Level('2-1', 0x28, 0x1f61, $this),
            '2-2' => new Level('2-2', 0x01, 0x2181, $this),
            '2-3' => new Level('2-3', 0x27, 0x1f4c, $this),
            '2-4' => new Level('2-4', 0x62, 0x1dc0, $this)
        ];
    }
}

class World3 extends World {
    protected $name = "World 3";

    public function __construct(Game $game, int $num) {
        parent::__construct($game, $num);
        $this->levels = [
            '3-1' => new Level('3-1', 0x24, 0x1ee0, $this),
            '3-2' => new Level('3-2', 0x30, 0x20c3, $this),
            '3-3' => new Level('3-3', 0x20, 0x1e69, $this),
            '3-4' => new Level('3-4', 0x63, 0x1def, $this)
        ];
        // add levels here
    }
}

class World4 extends World {
    protected $name = "World 4";

    public function __construct(Game $game, int $num) {
        parent::__construct($game, $num);
        // add levels here
    }
}

class World5 extends World {
    protected $name = "World 5";

    public function __construct(Game $game, int $num) {
        parent::__construct($game, $num);
        // add levels here
    }
}

class World6 extends World {
    protected $name = "World 6";

    public function __construct(Game $game, int $num) {
        parent::__construct($game, $num);
        // add levels here
    }
}

class World7 extends World {
    protected $name = "World 7";

    public function __construct(Game $game, int $num) {
        parent::__construct($game, $num);
        // add levels here
    }
}

class World8 extends World {
    protected $name = "World 8";

    public function __construct(Game $game, int $num) {
        parent::__construct($game, $num);
        // add levels here
    }
}
