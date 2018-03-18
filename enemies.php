<?php namespace SMBR;

$enemy = [
 'Green Koopa Troopa' => 0x00,
 'Red Koopa Troopa (walks off floors)' => 0x01,
 'Buzzy Beetle' => 0x02,
 'Red Koopa Troopa (stays on floors)' => 0x03,
 'Green Koopa Troopa (does not move)' => 0x04,
 'Hammer Bro' => 0x05,
 'Goomba' => 0x06,
 'Blooper' => 0x07,
 'Bullet Bill' => 0x08,
 'Yellow Koopa Paratroopa (does not move)' => 0x09,
 'Green Cheep Cheep (slow)' => 0x0A,
 'Red Cheep Cheep (fast)' => 0x0B,
 'Podoboo' => 0x0C,
 'Pirhana Plant' => 0x0D,
 'Green Koopa Paratroopa (leaping)' => 0x0E,
 'Red Koopa Troopa (down then up)' => 0x0F,
 'Green Koopa Troopa (left then right)' => 0x10,
 'Lakitu' => 0x11,
 'Red Flying Cheep-Cheep Generator' => 0x14,
 'Bowser Fire Generator' => 0x15,
 'Fireworks Generator' => 0x16,
 'Bullet Bill/Cheep-Cheep Generator' => 0x17,
 'Fire Bar (Clockwise)' => 0x1B,
 'Fast Fire Bar (Clockwise)' => 0x1C,
 'Fire Bar (Counter-Clockwise)' => 0x1D,
 'Fast Fire Bar (Counter-Clockwise)' => 0x1E,
 'Long Fire Bar (Clockwise)' => 0x1F,
 'Bowser' => 0x2D,
 'Toad' => 0x35,
 '2 Goombas V10' => 0x37,
 '3 Goombas V10' => 0x38,
 '2 Goombas V6' => 0x39,
 '3 Goombas V6' => 0x3A,
 '2 Green Koopa Troopas V10' => 0x3B,
 '3 Green Koopa Troopas V10' => 0x3C,
 '2 Green Koopa Troopas V6' => 0x3D,
 '3 Green Koopa Troopas V6' => 0x3E,
];

class Enemy {
    public $num;
    public function __construct($name) {
        global $enemy;
        $this->num = $enemy[$name];
    }
}
