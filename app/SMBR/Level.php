<?php 

namespace App\SMBR;


// TODO: add an array of valid values for level* with name/description,
// for prettier logging

class Level
{
    public $name = 'Unknown';
    public $map;
    public $enemy_data_offset;
    public $level_data_offset;
    public $pipe_pointers = [];
    public $midway_point;
    public $has_enemies = true;
    public $world_num = 0;

    protected $headerByte1 = 0;
    protected $headerByte2 = 0;
    protected $levelTime, $levelAutoWalk, $levelMarioY, $levelBackground, $levelCompliment, $levelScenery, $levelGround;

    protected static $levels;

    private $describeBackground = [ "Day time (normal palette)", "Underwater background", "Castle wall background", "Over water background",
                                    "Night time", "Day time / snow", "Night time / snow", "Black and white (castle)" ];
    private $describeScenery = [ "None", "Clouds", "Mountains / hills", "Fence / trees" ];
    private $describeCompliment = [ "Trees", "Mushrooms", "Bullet Bill turrets", "Clouds (bricks are clouds)" ];

    public function __construct($name, $map, $enemy_data_offset, $level_data_offset, $pipe_pointers, $midway_point, $enemies = true)
    {
        $this->name = $name;
        $this->map = $map;
        $this->enemy_data_offset = $enemy_data_offset;
        $this->level_data_offset = $level_data_offset;
        $this->pipe_pointers = $pipe_pointers;
        $this->midway_point = $midway_point;
        $this->has_enemies = $enemies;
        if (intval($name[0])) {
            $this->world_num = intval($name[0]) - 1;
        }
    }

    public function setHeaderBytes($byte1, $byte2)
    {
        $this->headerByte1 = $byte1;
        $this->headerByte2 = $byte2;
        $this->levelTime = ($byte1 & 0b11000000) >> 6;
        $this->levelAutoWalk = ($byte1 & 0b00100000) >> 5;
        $this->levelMarioY = ($byte1 & 0b00011000) >> 3;
        $this->levelBackground = ($byte1 & 0b00000111);
        $this->levelCompliment = ($byte2 & 0b11000000) >> 6;
        $this->levelScenery = ($byte2 & 0b00110000) >> 4;
        $this->levelGround = ($byte2 & 0b00001111);
    }

    public function getHeaderBytes()
    {
        $byte1 = ($this->levelTime << 6) | ($this->levelAutoWalk << 5) | ($this->levelMarioY << 3) | ($this->levelBackground);
        $byte2 = ($this->levelCompliment << 6) | ($this->levelScenery << 4) | ($this->levelGround);

        return [ $byte1, $byte2 ];
    }

    public function setTime($t)
    {
        if ($t > 3 || $t < 0) {
            print("Wrong value for levelTime\n");
            exit(1);
        }
        $this->levelTime = $t;
    }

    public function setScenery($s)
    {
        if ($s > 3 || $s < 0) {
            print("Wrong value for levelScenery\n");
            exit(1);
        }
        $this->levelScenery = $s;
    }

    public function setBackground($b)
    {
        if ($b > 7 || $b < 0) {
            print("Wrong value for levelBackground\n");
            exit(1);
        }
        $this->levelBackground = $b;
    }

    public function setCompliment($c)
    {
        if ($c > 3 || $c < 0) {
            print("Wrong value for levelCompliment\n");
            exit(1);
        }
        $this->levelCompliment = $c;
    }

    public function getBackgroundDescription()
    {
        return $this->describeBackground[$this->levelBackground];
    }

    public function getSceneryDescription()
    {
        return $this->describeScenery[$this->levelScenery];
    }

    public function getComplimentDescription()
    {
        return $this->describeCompliment[$this->levelCompliment];
    }


    public static function get(string $name)
    {
        $levels = static::all();
        foreach ($levels as $l) {
            if ($l->name == $name) {
                return $l;
            }
        }
    }

    public static function all()
    {
        if (static::$levels) {
            return static::$levels;
        }

        static::$levels = [
            //                 name   map   enemy   level   pipe pointers                                          midway point
            '1-1' => new Level('1-1', 0x25, 0x1f11, 0x269e, [[Pipe::get('1-1 Entry 1'), Pipe::get('1-1 Exit 1')]], 5),
            '1-2' => new Level('1-2', 0xc0, 0x20e8, 0x2c45, [
                [Pipe::get('1-2 Entry 1'), Pipe::get('1-2 Exit 1')],
                [Pipe::get('1-2 End'), null]], 6),
            '1-3' => new Level('1-3', 0x26, 0x1f2f, 0x2703, null, 4),
            '1-4' => new Level('1-4', 0x60, 0x1d80, 0x21bf, null, 0),
            '2-1' => new Level('2-1', 0x28, 0x1f61, 0x27dd, [
                [Pipe::get('2-1 Entry Cloud Area 1'), Pipe::get('2-1 Exit Cloud Area 1')],
                [Pipe::get('2-1 Entry 1'), Pipe::get('2-1 Exit 1')]], 6),
            '2-2' => new Level('2-2', 0x01, 0x2181, 0x2e55, [[Pipe::get('2-2 End'), null]], 5 /*0x2181 + 7, 0x2181 + 10 ]*/),
            '2-3' => new Level('2-3', 0x27, 0x1f4c, 0x2758, null, 7),
            '2-4' => new Level('2-4', 0x62, 0x1dc0, 0x229f, null, 0),
            '3-1' => new Level('3-1', 0x24, 0x1ee0, 0x2629, [
                [Pipe::get('3-1 Entry 1'), Pipe::get('3-1 Exit 1')],
                [Pipe::get('3-1 Entry Cloud Area 2'), Pipe::get('3-1 Exit Cloud Area 2')]], 6),
            '3-2' => new Level('3-2', 0x35, 0x20c3, 0x2c12, null, 6),
            '3-3' => new Level('3-3', 0x20, 0x1e69, 0x247b, null, 4),
            '3-4' => new Level('3-4', 0x63, 0x1def, 0x2312, null, 0),
            '4-1' => new Level('4-1', 0x22, 0x1eab, 0x2547, [[Pipe::get('4-1 Entry 1'), Pipe::get('4-1 Exit 1')]], 6),
            '4-2' => new Level('4-2', 0x41, 0x2115, 0x2ce8, [
                [Pipe::get('4-2 Warp Zone'), null],
                [Pipe::get('4-2 Entry 1'), Pipe::get('4-2 Exit 1')],
                [Pipe::get('4-2 End'), null]], 6),
            '4-3' => new Level('4-3', 0x2c, 0x1fb9, 0x289f, null, 4),
            '4-4' => new Level('4-4', 0x61, 0x1da7, 0x2220, null, 0),
            '5-1' => new Level('5-1', 0x2a, 0x1f8c, 0x284b, [[Pipe::get('5-1 Entry 1'), Pipe::get('5-1 Exit 1')]], 6),
            '5-2' => new Level('5-2', 0x31, 0x2045, 0x2aa2, [
                [Pipe::get('5-2 Entry Water Area'), Pipe::get('5-2 Exit Water Area')],
                [Pipe::get('5-2 Entry Cloud Area 1'), Pipe::get('5-2 Exit Cloud Area 1')]], 6),
            // level data set to 0 for levels that duplicate to avoid randomizing two times
            '5-3' => new Level('5-3', 0x26, 0x0000, 0x0000, null, 4, false),
            '5-4' => new Level('5-4', 0x62, 0x0000, 0x0000, null, 0, false),
            '6-1' => new Level('6-1', 0x2e, 0x2001, 0x296b, null, 6),
            '6-2' => new Level('6-2', 0x23, 0x1eb9, 0x259a, [
                [Pipe::get('6-2 Entry 1'), Pipe::get('6-2 Exit 1')],
                [Pipe::get('6-2 Entry Water Area'), Pipe::get('6-2 Exit Water Area')],
                [Pipe::get('6-2 Entry Cloud Area 2'), Pipe::get('6-2 Exit Cloud Area 2')],
                [Pipe::get('6-2 Entry 2'), Pipe::get('6-2 Exit 2')]], 6),
            '6-3' => new Level('6-3', 0x2d, 0x1fde, 0x2906, null, 6),
            '6-4' => new Level('6-4', 0x60, 0x0000, 0x0000, null, 0, false),
            '7-1' => new Level('7-1', 0x33, 0x209e, 0x2b8e, [[Pipe::get('7-1 Entry 1'), Pipe::get('7-1 Exit 1')]], 6),
            '7-2' => new Level('7-2', 0x01, 0x0000, 0x0000, [[Pipe::get('7-2 End'), null]], 5, false),
            '7-3' => new Level('7-3', 0x27, 0x0000, 0x0000, null, 7, false),
            '7-4' => new Level('7-4', 0x64, 0x1e1a, 0x237f, null, 0),
            '8-1' => new Level('8-1', 0x30, 0x200b, 0x2a0f, [[Pipe::get('8-1 Entry 1'), Pipe::get('8-1 Exit 1')]], 0),
            '8-2' => new Level('8-2', 0x32, 0x2070, 0x2b15, [[Pipe::get('8-2 Entry 1'), Pipe::get('8-2 Exit 1')]], 0),
            '8-3' => new Level('8-3', 0x21, 0x1e8e, 0x24de, null, 0),
            '8-4' => new Level('8-4', 0x65, 0x1e2f, 0x240a, null, 0),
            'Pipe Transition' => new Level('Pipe Transition', 0x29, 0x0000, 0x0000, [], -1, false), // what is enemy offset?
            'Cloud Area 1' => new Level('cloud-area-1', 0x2b, 0x1fb0, 0x0000, [], -1, false),
            'Cloud Area 2' => new Level('cloud-area-2', 0x34, 0x20ba, 0x0000, [], -1, false),
            'Water Area' => new Level('water-area', 0x00, 0x2170, 0x0000, [], -1, false),
            'Water Area 8-4' => new Level('water-8-4', 0x02, 0x21ab, 0x0000, [], -1, false),
            'Underground Bonus' => new Level('underground-bonus', 0x42, 0x2143, 0x2d89, [], -1, false), // pretty sure this is 0x42, but c2 also works.......
            '4-2 Warp Zone' => new Level('warp-zone-4-2', 0x2f, 0x200a, 0x0000, [], -1, false),
        ];

        return static::all();
    }
}
