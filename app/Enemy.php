<?php namespace SMBR;

/*
 * Inspired by the alttp randomizer
 *
 * but my implementation is a bit bad
 * get should return enemy object
 * getNum? should return number
 */

class Enemy
{
    protected $name;
    protected $num;

    protected static $enemies;

    public function __construct($name, $num)
    {
        $this->name = $name;
        $this->num = $num;
    }

    public static function get(string $name)
    {
        $enemies = static::all();
        foreach ($enemies as $e) {
            if ($e->name == $name) {
                return $e->num;
            }
        }
    }

    public static function getName($num)
    {
        $enemies = static::all();
        foreach ($enemies as $e) {
            if ($e->num == $num) {
                return $e->name;
            }
        }
    }

    public static function all()
    {
        if (static::$enemies) {
            return static::$enemies;
        }

        static::$enemies = [
            new Enemy('Green Koopa Troopa', 0x00),
            new Enemy('Red Koopa Troopa (walks off floors)', 0x01),
            new Enemy('Buzzy Beetle', 0x02),
            new Enemy('Red Koopa Troopa (stays on floors)', 0x03),
            new Enemy('Green Koopa Troopa (does not move)', 0x04),
            new Enemy('Hammer Bro', 0x05),
            new Enemy('Goomba', 0x06),
            new Enemy('Blooper', 0x07),
            new Enemy('Bullet Bill', 0x08),
            new Enemy('Yellow Koopa Paratroopa (does not move)', 0x09),
            new Enemy('Green Cheep-Cheep (slow)', 0x0A),
            new Enemy('Red Cheep-Cheep (fast)', 0x0B),
            new Enemy('Podoboo', 0x0C),
            new Enemy('Pirhana Plant', 0x0D),
            new Enemy('Green Koopa Paratroopa (leaping)', 0x0E),
            new Enemy('Red Koopa Troopa (down then up)', 0x0F),
            new Enemy('Green Koopa Troopa (left then right)', 0x10),
            new Enemy('Lakitu', 0x11),
            new Enemy('Spiny', 0x12),
            new Enemy('Red Flying Cheep-Cheep Generator', 0x14),
            new Enemy('Bowser Fire Generator', 0x15),
            new Enemy('Fireworks Generator', 0x16),
            new Enemy('Bullet Bill/Cheep-Cheep Generator', 0x17),
            new Enemy('Fire Bar (Clockwise)', 0x1B),
            new Enemy('Fast Fire Bar (Clockwise)', 0x1C),
            new Enemy('Fire Bar (Counter-Clockwise)', 0x1D),
            new Enemy('Fast Fire Bar (Counter-Clockwise)', 0x1E),
            new Enemy('Long Fire Bar (Clockwise)', 0x1F),
            new Enemy('Lift (Balance)', 0x24),
            new Enemy('Lift (Down Up)', 0x25),
            new Enemy('Lift (Up)', 0x26),
            new Enemy('Lift (Down)', 0x27),
            new Enemy('Lift (Left Right)', 0x28),
            new Enemy('Lift (Fall)', 0x29),
            new Enemy('Lift (Right)', 0x2A),
            new Enemy('Short Lift (Up)', 0x2B),
            new Enemy('Short Lift (Down)', 0x2C),
            new Enemy('Bowser', 0x2D),
            new Enemy('Warp Zone', 0x34),
            new Enemy('Toad', 0x35),
            new Enemy('2 Goombas V10', 0x37),
            new Enemy('3 Goombas V10', 0x38),
            new Enemy('2 Goombas V6', 0x39), // V6 = higher up
            new Enemy('3 Goombas V6', 0x3A),
            new Enemy('2 Green Koopa Troopas V10', 0x3B),
            new Enemy('3 Green Koopa Troopas V10', 0x3C),
            new Enemy('2 Green Koopa Troopas V6', 0x3D),
            new Enemy('3 Green Koopa Troopas V6', 0x3E),
        ];

        return static::all();
    }
}
