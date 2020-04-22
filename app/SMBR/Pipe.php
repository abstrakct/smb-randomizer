<?php 

namespace App\SMBR;

class Pipe
{
    public $name;
    protected $offset, $byte1, $byte2, $byte3;
    protected $x, $y, $new_page_flag, $map_pointer, $world_active, $page_pointer;

    protected static $pipes;

    public function __construct($n, $o, $b1, $b2, $b3)
    {
        $this->name = $n;
        $this->offset = $o;
        $this->byte1 = $b1;
        $this->byte2 = $b2;
        $this->byte3 = $b3;
        $this->x = ($b1 & 0b11110000) >> 4;
        $this->y = ($b1 & 0b00001111);
        $this->new_page_flag = ($b2 & 0b10000000) >> 7;
        $this->map_pointer = ($b2 & 0b01111111);
        $this->world_active = ($b3 & 0b11100000) >> 5;
        $this->page_pointer = ($b3 & 0b00011111);
    }

    public function translateValuesToBytes()
    {
        $byte1 = ($this->x << 4) | $this->y;
        $byte2 = ($this->new_page_flag << 7) | $this->map_pointer;
        $byte3 = ($this->world_active << 5) | $this->page_pointer;

        return [$byte1, $byte2, $byte3];
    }

    public function setWorldActive($world)
    {
        $this->world_active = $world;
    }

    public function setPage($page)
    {
        $this->page_pointer = $page;
    }

    public function setMap($map)
    {
        $this->map_pointer = $map;
    }

    public function setNewPageFlag($flag)
    {
        $this->new_page_flag = $flag;
    }

    public function setX($x)
    {
        $this->x = $x;
    }

    public function setY($y)
    {
        $this->y = $y;
    }

    public function setOffset($offset)
    {
        $this->offset = $offset;
    }

    public function getWorldActive()
    {
        return $this->world_active;
    }

    public function getPage()
    {
        return $this->page_pointer;
    }

    public function getMap()
    {
        return $this->map_pointer;
    }

    public function getNewPageFlag()
    {
        return $this->new_page_flag;
    }

    public function getX()
    {
        return $this->x;
    }

    public function getY()
    {
        return $this->y;
    }

    public function getOffset()
    {
        return $this->offset;
    }

    public static function get(string $name)
    {
        $pipes = static::all();
        foreach ($pipes as $p) {
            if ($p->name == $name) {
                return $p;
            }
        }
    }

    // TODO: add vanilla data array, preferably as consts or at least not changeable, only readable
    public static function all()
    {
        if (static::$pipes) {
            return static::$pipes;
        }

        // TODO: double check byte values
        static::$pipes = [
            '1-1 Entry 1' => new Pipe('1-1 Entry 1', 0x1f11, 0x1e, 0xc2, 0x00),
            '1-1 Exit 1' => new Pipe('1-1 Exit 1', 0x2143, 0x1e, 0xa5, 0x0a),
            '1-2 Entry 1' => new Pipe('1-2 Entry 1', 0x20ee, 0xde, 0xc2, 0x02),
            '1-2 Exit 1' => new Pipe('1-2 Exit 1', 0x214e, 0x1e, 0x40, 0x07),
            '1-2 End' => new Pipe('1-2 End', 0x210f, 0xee, 0x25, 0x0b),
            '2-1 Entry Cloud Area 1' => new Pipe('2-1 Entry Cloud Area 1', 0x1f6d, 0xfe, 0x2b, 0x20),
            '2-1 Exit Cloud Area 1' => new Pipe('2-1 Exit Cloud Area 1', 0x1fb2, 0x0e, 0x28, 0x2a),
            '2-1 Entry 1' => new Pipe('2-1 Entry 1', 0x1f78, 0xfe, 0x42, 0x20),
            '2-1 Exit 1' => new Pipe('2-1 Exit 1', 0x2146, 0x2e, 0x28, 0x27),
            '2-2 End' => new Pipe('2-2 End', 0x2183, 0x2e, 0x25, 0x2b),
            '3-1 Entry 1' => new Pipe('3-1 Entry 1', 0x1ee4, 0xee, 0x42, 0x44),
            '3-1 Exit 1' => new Pipe('3-1 Exit 1', 0x2156, 0x1e, 0x24, 0x44),
            '3-1 Entry Cloud Area 2' => new Pipe('3-1 Entry Cloud Area 2', 0x1ef7, 0xfe, 0x34, 0x40),
            '3-1 Exit Cloud Area 2' => new Pipe('3-1 Exit Cloud Area 2', 0x20bc, 0x0e, 0x24, 0x4a),
            '4-1 Entry 1' => new Pipe('4-1 Entry 1', 0x1eab, 0x2e, 0xc2, 0x66),
            '4-1 Exit 1' => new Pipe('4-1 Exit 1', 0x215b, 0x1e, 0x22, 0x6a),
            '4-2 Warp Zone' => new Pipe('4-2 Warp Zone', 0x2117, 0x1e, 0x2f, 0x60),
            '4-2 Entry 1' => new Pipe('4-2 Entry 1', 0x2124, 0xfe, 0x42, 0x68),
            '4-2 Exit 1' => new Pipe('4-2 Exit 1', 0x2163, 0x1e, 0x41, 0x68),
            '4-2 End' => new Pipe('4-2 End', 0x213f, 0xee, 0x25, 0x6b),
            '5-1 Entry 1' => new Pipe('5-1 Entry 1', 0x1fa6, 0xee, 0x42, 0x88),
            '5-1 Exit 1' => new Pipe('5-1 Exit 1', 0x2166, 0x1e, 0x2a, 0x8a),
            '5-2 Entry Water Area' => new Pipe('5-2 Entry Water Area', 0x2047, 0xae, 0x00, 0x80),
            '5-2 Exit Water Area' => new Pipe('5-2 Exit Water Area', 0x2176, 0xee, 0x31, 0x87),
            '5-2 Entry Cloud Area 1' => new Pipe('5-2 Entry Cloud Area 1', 0x2054, 0x4e, 0x2b, 0x80),
            '5-2 Exit Cloud Area 1' => new Pipe('5-2 Exit Cloud Area 1', 0x1fb5, 0x0e, 0x31, 0x88),
            '6-2 Entry 1' => new Pipe('6-2 Entry 1', 0x1eb9, 0x0e, 0xc2, 0xa8),
            '6-2 Exit 1' => new Pipe('6-2 Exit 1', 0x2169, 0x2e, 0x23, 0xa2),
            '6-2 Entry Water Area' => new Pipe('6-2 Entry Water Area', 0x1ec2, 0xde, 0x00, 0xa0),
            '6-2 Exit Water Area' => new Pipe('6-2 Exit Water Area', 0x2179, 0xee, 0x23, 0xa7),
            '6-2 Entry Cloud Area 2' => new Pipe('6-2 Entry Cloud Area 2', 0x1ec9, 0x3e, 0xb4, 0xa0),
            '6-2 Exit Cloud Area 2' => new Pipe('6-2 Exit Cloud Area 2', 0x20bf, 0x1e, 0x23, 0xaa),
            '6-2 Entry 2' => new Pipe('6-2 Entry 2', 0x1ed0, 0x7e, 0x42, 0xa6),
            '6-2 Exit 2' => new Pipe('6-2 Exit 2', 0x215e, 0x2e, 0x23, 0xab),
            '7-1 Entry 1' => new Pipe('7-1 Entry 1', 0x20a0, 0xde, 0x42, 0xc0),
            '7-1 Exit 1' => new Pipe('7-1 Exit 1', 0x2149, 0x2e, 0x33, 0xc7),
            '7-2 End' => new Pipe('7-2 End', 0x2189, 0x4e, 0x25, 0xcb),
            '8-1 Entry 1' => new Pipe('8-1 Entry 1', 0x200f, 0xde, 0x42, 0xe2),
            '8-1 Exit 1' => new Pipe('8-1 Exit 1', 0x2151, 0x2e, 0x30, 0xe7),
            '8-2 Entry 1' => new Pipe('8-2 Entry 1', 0x2088, 0xfe, 0x42, 0xe8),
            '8-2 Exit 1' => new Pipe('8-2 Exit 1', 0x216c, 0x2e, 0x32, 0xea),
        ];

        return static::all();
    }
}

class VanillaPipe
{
    public $name;
    protected $offset, $byte1, $byte2, $byte3;
    protected $x, $y, $new_page_flag, $map_pointer, $world_active, $page_pointer;

    protected static $pipes;

    public function __construct($n, $o, $b1, $b2, $b3)
    {
        $this->name = $n;
        $this->offset = $o;
        $this->byte1 = $b1;
        $this->byte2 = $b2;
        $this->byte3 = $b3;
        $this->x = ($b1 & 0b11110000) >> 4;
        $this->y = ($b1 & 0b00001111);
        $this->new_page_flag = ($b2 & 0b10000000) >> 7;
        $this->map_pointer = ($b2 & 0b01111111);
        $this->world_active = ($b3 & 0b11100000) >> 5;
        $this->page_pointer = ($b3 & 0b00011111);
    }

    public function getWorldActive()
    {
        return $this->world_active;
    }

    public function getPage()
    {
        return $this->page_pointer;
    }

    public function getMap()
    {
        return $this->map_pointer;
    }

    public function getNewPageFlag()
    {
        return $this->new_page_flag;
    }

    public function getX()
    {
        return $this->x;
    }

    public function getY()
    {
        return $this->y;
    }

    public function getOffset()
    {
        return $this->offset;
    }

    public static function get(string $name)
    {
        $pipes = static::all();
        foreach ($pipes as $p) {
            if ($p->name == $name) {
                return $p;
            }
        }
    }

    // TODO: add vanilla data array, preferably as consts or at least not changeable, only readable
    public static function all()
    {
        if (static::$pipes) {
            return static::$pipes;
        }

        // TODO: double check byte values
        static::$pipes = [
            '1-1 Entry 1' => new VanillaPipe('1-1 Entry 1', 0x1f11, 0x1e, 0xc2, 0x00),
            '1-1 Exit 1' => new VanillaPipe('1-1 Exit 1', 0x2143, 0x1e, 0xa5, 0x0a),
            '1-2 Entry 1' => new VanillaPipe('1-2 Entry 1', 0x20ee, 0xde, 0xc2, 0x02),
            '1-2 Exit 1' => new VanillaPipe('1-2 Exit 1', 0x214e, 0x1e, 0x40, 0x07),
            '1-2 End' => new VanillaPipe('1-2 End', 0x210f, 0xee, 0x25, 0x0b),
            '2-1 Entry Cloud Area 1' => new VanillaPipe('2-1 Entry Cloud Area 1', 0x1f6d, 0xfe, 0x2b, 0x20),
            '2-1 Exit Cloud Area 1' => new VanillaPipe('2-1 Exit Cloud Area 1', 0x1fb2, 0x0e, 0x28, 0x2a),
            '2-1 Entry 1' => new VanillaPipe('2-1 Entry 1', 0x1f78, 0xfe, 0x42, 0x20),
            '2-1 Exit 1' => new VanillaPipe('2-1 Exit 1', 0x2146, 0x2e, 0x28, 0x27),
            '2-2 End' => new VanillaPipe('2-2 End', 0x2183, 0x2e, 0x25, 0x2b),
            '3-1 Entry 1' => new VanillaPipe('3-1 Entry 1', 0x1ee4, 0xee, 0x42, 0x44),
            '3-1 Exit 1' => new VanillaPipe('3-1 Exit 1', 0x2156, 0x1e, 0x24, 0x44),
            '3-1 Entry Cloud Area 2' => new VanillaPipe('3-1 Entry Cloud Area 2', 0x1ef7, 0xfe, 0x34, 0x40),
            '3-1 Exit Cloud Area 2' => new VanillaPipe('3-1 Exit Cloud Area 2', 0x20bc, 0x0e, 0x24, 0x4a),
            '4-1 Entry 1' => new VanillaPipe('4-1 Entry 1', 0x1eab, 0x2e, 0xc2, 0x66),
            '4-1 Exit 1' => new VanillaPipe('4-1 Exit 1', 0x215b, 0x1e, 0x22, 0x6a),
            '4-2 Warp Zone' => new VanillaPipe('4-2 Warp Zone', 0x2117, 0x1e, 0x2f, 0x60),
            '4-2 Entry 1' => new VanillaPipe('4-2 Entry 1', 0x2124, 0xfe, 0x42, 0x68),
            '4-2 Exit 1' => new VanillaPipe('4-2 Exit 1', 0x2163, 0x1e, 0x41, 0x68),
            '4-2 End' => new VanillaPipe('4-2 End', 0x213f, 0xee, 0x25, 0x6b),
            '5-1 Entry 1' => new VanillaPipe('5-1 Entry 1', 0x1fa6, 0xee, 0x42, 0x88),
            '5-1 Exit 1' => new VanillaPipe('5-1 Exit 1', 0x2166, 0x1e, 0x2a, 0x8a),
            '5-2 Entry Water Area' => new VanillaPipe('5-2 Entry Water Area', 0x2047, 0xae, 0x00, 0x80),
            '5-2 Exit Water Area' => new VanillaPipe('5-2 Exit Water Area', 0x2176, 0xee, 0x31, 0x87),
            '5-2 Entry Cloud Area 1' => new VanillaPipe('5-2 Entry Cloud Area 1', 0x2054, 0x4e, 0x2b, 0x80),
            '5-2 Exit Cloud Area 1' => new VanillaPipe('5-2 Exit Cloud Area 1', 0x1fb5, 0x0e, 0x31, 0x88),
            '6-2 Entry 1' => new VanillaPipe('6-2 Entry 1', 0x1eb9, 0x0e, 0xc2, 0xa8),
            '6-2 Exit 1' => new VanillaPipe('6-2 Exit 1', 0x2169, 0x2e, 0x23, 0xa2),
            '6-2 Entry Water Area' => new VanillaPipe('6-2 Entry Water Area', 0x1ec2, 0xde, 0x00, 0xa0),
            '6-2 Exit Water Area' => new VanillaPipe('6-2 Exit Water Area', 0x2179, 0xee, 0x23, 0xa7),
            '6-2 Entry Cloud Area 2' => new VanillaPipe('6-2 Entry Cloud Area 2', 0x1ec9, 0x3e, 0xb4, 0xa0),
            '6-2 Exit Cloud Area 2' => new VanillaPipe('6-2 Exit Cloud Area 2', 0x20bf, 0x1e, 0x23, 0xaa),
            '6-2 Entry 2' => new VanillaPipe('6-2 Entry 2', 0x1ed0, 0x7e, 0x42, 0xa6),
            '6-2 Exit 2' => new VanillaPipe('6-2 Exit 2', 0x215e, 0x2e, 0x23, 0xab),
            '7-1 Entry 1' => new VanillaPipe('7-1 Entry 1', 0x20a0, 0xde, 0x42, 0xc0),
            '7-1 Exit 1' => new VanillaPipe('7-1 Exit 1', 0x2149, 0x2e, 0x33, 0xc7),
            '7-2 End' => new VanillaPipe('7-2 End', 0x2189, 0x4e, 0x25, 0xcb),
            '8-1 Entry 1' => new VanillaPipe('8-1 Entry 1', 0x200f, 0xde, 0x42, 0xe2),
            '8-1 Exit 1' => new VanillaPipe('8-1 Exit 1', 0x2151, 0x2e, 0x30, 0xe7),
            '8-2 Entry 1' => new VanillaPipe('8-2 Entry 1', 0x2088, 0xfe, 0x42, 0xe8),
            '8-2 Exit 1' => new VanillaPipe('8-2 Exit 1', 0x216c, 0x2e, 0x32, 0xea),
        ];

        return static::all();
    }
}
