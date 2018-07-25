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
    'Green Cheep-Cheep (slow)' => 0x0A,
    'Red Cheep-Cheep (fast)' => 0x0B,
    'Podoboo' => 0x0C,
    'Pirhana Plant' => 0x0D,
    'Green Koopa Paratroopa (leaping)' => 0x0E,
    'Red Koopa Troopa (down then up)' => 0x0F,
    'Green Koopa Troopa (left then right)' => 0x10,
    'Lakitu' => 0x11,
    'Spiny' => 0x12,
    'Red Flying Cheep-Cheep Generator' => 0x14,
    'Bowser Fire Generator' => 0x15,
    'Fireworks Generator' => 0x16,
    'Bullet Bill/Cheep-Cheep Generator' => 0x17,
    'Fire Bar (Clockwise)' => 0x1B,
    'Fast Fire Bar (Clockwise)' => 0x1C,
    'Fire Bar (Counter-Clockwise)' => 0x1D,
    'Fast Fire Bar (Counter-Clockwise)' => 0x1E,
    'Long Fire Bar (Clockwise)' => 0x1F,
    'Lift (Balance)' => 0x24,
    'Lift (Down Up)' => 0x25,
    'Lift (Up)' => 0x26,
    'Lift (Down)' => 0x27,
    'Lift (Left Right)' => 0x28,
    'Lift (Fall)' => 0x29,
    'Lift (Right)' => 0x2A,
    'Short Lift (Up)' => 0x2B,
    'Short Lift (Down)' => 0x2C,
    'Bowser' => 0x2D,
    'Warp Zone' => 0x34,
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

$enemydataoffsets = [
    '1-4' => 0x1d80, '6-4' => 0x1d80, '4-4' => 0x1da7, '2-4' => 0x1dc0, '5-4' => 0x1dc0, '3-4' => 0x1def, '7-4' => 0x1e1a, '8-4' => 0x1e2f,
    '3-3' => 0x1e69, '8-3' => 0x1e8e, '4-1' => 0x1eab, '6-2' => 0x1eb9, '3-1' => 0x1ee0, '1-1' => 0x1f11, '1-3' => 0x1f2f, '5-3' => 0x1f2f,
    '2-3' => 0x1f4c, '7-3' => 0x1f4c, '2-1' => 0x1f61, '5-1' => 0x1f8c, '4-3' => 0x1fb9, '6-3' => 0x1fde, '6-1' => 0x2001, '8-1' => 0x200b,
    '5-2' => 0x2045, '8-2' => 0x2070, '7-1' => 0x209e, '3-2' => 0x20c3, '1-2' => 0x20e8, '4-2' => 0x2115, '2-2' => 0x2181, '7-2' => 0x2181,
    '2-1-cloud' => 0x1fb0,
    '3-1-cloud' => 0x20ba,
    '5-2-cloud' => 0x1fb0,
    '6-2-cloud' => 0x20ba,
    '5-2-water' => 0x2170,
    '6-2-water' => 0x2170,
    '8-4-water' => 0x21ab,
    'underground-bonus' => 0x2143,
];

/* $enemy_data_offsets_for_shuffling = [
'1-4 6-4' => 0x1d80, '4-4' => 0x1da7, '2-4 5-4' => 0x1dc0, '3-4' => 0x1def, '7-4' => 0x1e1a, '8-4' => 0x1e2f,
'3-3' => 0x1e69, '8-3' => 0x1e8e, '4-1' => 0x1eab, '6-2' => 0x1eb9, '3-1' => 0x1ee0, '1-1' => 0x1f11, '1-3 5-3' => 0x1f2f,
'2-3 7-3' => 0x1f4c, '2-1' => 0x1f61, '5-1' => 0x1f8c, '4-3' => 0x1fb9, '6-3' => 0x1fde, '6-1' => 0x2001, '8-1' => 0x200b,
'5-2' => 0x2045, '8-2' => 0x2070, '7-1' => 0x209e, '3-2' => 0x20c3, '1-2' => 0x20e8, '4-2' => 0x2115, '2-2 7-2' => 0x2181,
]; */

class Enemy
{
    public $num;
    public function __construct($name)
    {
        global $enemy;
        $this->num = $enemy[$name];
    }
}
