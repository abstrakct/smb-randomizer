<?php namespace SMBR;

class Level {
    public $name = 'Unknown';
    public $world;
    public $map;
    public $enemy_data_offset;
    public $is_duplicate;
    public $twin;
    public $pipe_pointers = [];

    public function __construct($name, $map, $edo, $world, $pp = null, $dupe = false, $twin = null) {
        $this->name = $name;
        $this->map = $map;
        $this->enemy_data_offset = $edo;
        $this->world = $world;
        $this->pipe_pointers = $pp;
        $this->is_duplicate = $dupe;
        $this->twin = $twin;
    }

    public function getName() {
        return $this->name;
    }
}

// TODO: better handling of maps that are used twice!!!!!!!! keep track of changes or something
// TODO: handle pipes used in different worlds!!
$vanilla_level = [ 
    //                 name   map   enemy  $world   pipes  dupe  twin
    '1-1' => new Level('1-1', 0x25, 0x1f11, null, [ 'offsets' => [ 0x1f11 + 2, 0x2143 + 2 ] ]),
                                                                            //TODO FIXXXX
    '1-2' => new Level('1-2', 0xc0, 0x20e8, null, [ 'offsets' => [ 0x20e8 + 8, 0x2143 + 13, 0x20e8 + 41, null ] ]),
    '1-3' => new Level('1-3', 0x26, 0x1f2f, null,   null, true, '5-3'),
    '1-4' => new Level('1-4', 0x60, 0x1d80, null,   null, true, '6-4'),
    '2-1' => new Level('2-1', 0x28, 0x1f61, null, [ 'offsets' => [ 0x1f61 + 14, 0x1fb0 + 4, 0x1f61 + 25, 0x2143 + 5 ] ]),
    '2-2' => new Level('2-2', 0x01, 0x2181, null, [ 'offsets' => [ 0x2181 + 4, null ] ] /*0x2181 + 7, 0x2181 + 10 ]*/),
    '2-3' => new Level('2-3', 0x27, 0x1f4c, null,   null, true, '7-3'),
    '2-4' => new Level('2-4', 0x62, 0x1dc0, null,   null, true, '5-4'),
    '3-1' => new Level('3-1', 0x24, 0x1ee0, null, [ 'offsets' => [ 0x1ee0 + 6, 0x2143 + 21, 0x1ee0 + 25, 0x20ba + 4  ] ]),
    '3-2' => new Level('3-2', 0x35, 0x20c3, null),
    '3-3' => new Level('3-3', 0x20, 0x1e69, null),
    '3-4' => new Level('3-4', 0x63, 0x1def, null),
    '4-1' => new Level('4-1', 0x22, 0x1eab, null, [ 'offsets' => [ 0x1eab + 2, 0x2143 + 26 ] ]),
    '4-2' => new Level('4-2', 0x41, 0x2115, null, [ 'offsets' => [ 0x2115 + 4, null, 0x2115 + 17, 0x2143 + 34, 0x2115 + 44, null ] ]),  // not sure if last null is correct, but probably (warp zone)
    '4-3' => new Level('4-3', 0x2c, 0x1fb9, null),
    '4-4' => new Level('4-4', 0x61, 0x1da7, null),
    '5-1' => new Level('5-1', 0x2a, 0x1f8c, null, [ 'offsets' => [ 0x1f8c + 28, 0x2143 + 37 ] ]),
    '5-2' => new Level('5-2', 0x31, 0x2045, null, [ 'offsets' => [ 0x2045 + 4,  0x2170 + 8, 0x2045 + 17, 0x1fb0 + 7 ] ]),
    '5-3' => new Level('5-3', 0x26, 0x1f2f, null),
    '5-4' => new Level('5-4', 0x62, 0x1dc0, null),
    '6-1' => new Level('6-1', 0x2e, 0x2001, null),
    '6-2' => new Level('6-2', 0x23, 0x1eb9, null, [ 'offsets' => [ 0x1eb9 + 2, 0x2143 + 29, 0x1eb9 + 11, 0x2170 + 11, 0x1eb9 + 18, 0x20ba + 7, 0x1eb9 + 25, 0x2143 + 40 ] ]),  // first, last exit unsure
    '6-3' => new Level('6-3', 0x2d, 0x1fde, null),
    '6-4' => new Level('6-4', 0x60, 0x1d80, null),
    '7-1' => new Level('7-1', 0x33, 0x209e, null, [ 'offsets' => [ 0x209e + 4, 0x2143 + 8 ] ]),
    '7-2' => new Level('7-2', 0x01, 0x2181, null, [ 'offsets' => [ 0x2181 + 10, null ] ]),
    '7-3' => new Level('7-3', 0x27, 0x1f4c, null),
    '7-4' => new Level('7-4', 0x64, 0x1e1a, null),
    '8-1' => new Level('8-1', 0x30, 0x200b, null, [ 'offsets' => [ 0x200b + 6,  0x2143 + 16 ] ]),
    '8-2' => new Level('8-2', 0x32, 0x2070, null, [ 'offsets' => [ 0x2070 + 26, 0x2143 + 43 ] ]),
    '8-3' => new Level('8-3', 0x21, 0x1e8e, null),
    '8-4' => new Level('8-4', 0x65, 0x1e2f, null),
    'Pipe Transition' =>   new Level('Pipe Transition',   0x29, null, null), // what is enemy offset?
    'Cloud Area 1' =>      new Level('cloud-area-1',      0x2b, 0x1fb0, null, []),
    'Cloud Area 2' =>      new Level('cloud-area-2',      0x34, 0x20ba, null, []),
    'Water Area' =>        new Level('water-area',        0x00, 0x2170, null, []),
    'Water Area 8-4' =>    new Level('water-8-4',         0x02, 0x21ab, null, []),
    'Underground Bonus' => new Level('underground-bonus', 0x42, 0x2143, null, []),  // pretty sure this is 0x42, but c2 also works.......
    '4-2 Warp Zone' =>     new Level('4-2-warp-zone',     0x2f, 0x200a, null),
];

function mapToName($map) {
    global $vanilla_level;
    foreach ($vanilla_level as $l) {
        if($l->map == $map)
            return $l->name;
    }
    
    return "unknown!";
}
/*
 * from disassembly:
;bonus area data offsets, included here for comparison purposes
;underground bonus area  - c2
;cloud area 1 (day)      - 2b
;cloud area 2 (night)    - 34
;water area (5-2/6-2)    - 00
;water area (8-4)        - 02
;warp zone area (4-2)    - 2f
 */ 
