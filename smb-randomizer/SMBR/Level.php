<?php namespace SMBR;

class LevelDefinition {
    public $name;
    public $map;
    public $enemy_data_offset;
    public $level_data_offset;
    public $pipe_pointers = [];
    public $midway_point;

    public function __construct($name, $map, $edo, $ldo, $pp = null, $mp = -1) {
        $this->name = $name;
        $this->map = $map;
        $this->enemy_data_offset = $edo;
        $this->level_data_offset = $ldo;
        $this->pipe_pointers = $pp;
        $this->midway_point = $mp;
    }
}

class Level {
    public $name = 'Unknown';
    public $map;
    public $enemy_data_offset;
    public $level_data_offset;
    public $pipe_pointers = [];
    public $midway_point;

    public function __construct($leveldef) {
        $this->name = $leveldef->name;
        $this->map = $leveldef->map;
        $this->enemy_data_offset = $leveldef->enemy_data_offset;
        $this->level_data_offset = $leveldef->level_data_offset;
        $this->pipe_pointers = $leveldef->pipe_pointers;
        $this->midway_point = $leveldef->midway_point;
    }

    public function getName() {
        return $this->name;
    }
}

// UNUSED WORKING MAPS
// 0x03 - psychdelic flooded world
// 0x05 - 0xb similar
//
// 0x36 = 1-2 w/ overworld graphics....
// etc.
//
//
// TODO: better handling of maps that are used twice!!!!!!!! keep track of changes or something - OR DO WE NEED TO?? probably not
$vanilla_level = [ 
    //                           name   map   enemy   level   pipe pointers                   midway point
    '1-1' => new LevelDefinition('1-1', 0x25, 0x1f11, 0x269e, [ [ 0x1f11 +  2, 0x2143 + 2 ] ],  5),
    '1-2' => new LevelDefinition('1-2', 0xc0, 0x20e8, 0x2c45, [ [ 0x20e8 +  8, 0x2143 + 13 ],
                                                            [ 0x20e8 + 41, null ] ],        6),
    '1-3' => new LevelDefinition('1-3', 0x26, 0x1f2f, 0x2703, null, 4),
    '1-4' => new LevelDefinition('1-4', 0x60, 0x1d80, 0x21bf, null, 0),
    '2-1' => new LevelDefinition('2-1', 0x28, 0x1f61, 0x27dd, [ [ 0x1f61 + 14, 0x1fb0 + 4 ],
                                                            [ 0x1f61 + 25, 0x2143 + 5 ] ],  6),
    '2-2' => new LevelDefinition('2-2', 0x01, 0x2181, 0x2e55, [ [ 0x2181 +  4, null ] ],        5 /*0x2181 + 7, 0x2181 + 10 ]*/),
    '2-3' => new LevelDefinition('2-3', 0x27, 0x1f4c, 0x2758, null, 7),
    '2-4' => new LevelDefinition('2-4', 0x62, 0x1dc0, 0x229f, null, 0),
    '3-1' => new LevelDefinition('3-1', 0x24, 0x1ee0, 0x2629, [ [ 0x1ee0 +  6, 0x2143 + 21 ],
                                                            [ 0x1ee0 + 25, 0x20ba + 4  ] ], 6),
    '3-2' => new LevelDefinition('3-2', 0x35, 0x20c3, 0x2c12, null, 6),
    '3-3' => new LevelDefinition('3-3', 0x20, 0x1e69, 0x247b, null, 4),
    '3-4' => new LevelDefinition('3-4', 0x63, 0x1def, 0x2312, null, 0),
    '4-1' => new LevelDefinition('4-1', 0x22, 0x1eab, 0x2547, [ [ 0x1eab +  2, 0x2143 + 26 ] ], 6),
    '4-2' => new LevelDefinition('4-2', 0x41, 0x2115, 0x2ce8, [ [ 0x2115 +  4, null ],
                                                            [ 0x2115 + 17, 0x2143 + 34 ],
                                                            [ 0x2115 + 44, null ] ], 6),  // not sure if last null is correct, but probably (warp zone)
    '4-3' => new LevelDefinition('4-3', 0x2c, 0x1fb9, 0x289f, null, 4),
    '4-4' => new LevelDefinition('4-4', 0x61, 0x1da7, 0x2220, null, 0),
    '5-1' => new LevelDefinition('5-1', 0x2a, 0x1f8c, 0x284b, [ [ 0x1f8c + 28, 0x2143 + 37 ] ], 6),
    '5-2' => new LevelDefinition('5-2', 0x31, 0x2045, 0x2aa2, [ [ 0x2045 + 4,  0x2170 + 8 ],
                                                            [ 0x2045 + 17, 0x1fb0 + 7 ] ], 6),
    // level data set to 0 for levels that duplicate to avoid randomizing two times
    '5-3' => new LevelDefinition('5-3', 0x26, 0x0000, 0x0000, null, 4),
    '5-4' => new LevelDefinition('5-4', 0x62, 0x0000, 0x0000, null, 0),
    '6-1' => new LevelDefinition('6-1', 0x2e, 0x2001, 0x296b, null, 6),
    '6-2' => new LevelDefinition('6-2', 0x23, 0x1eb9, 0x259a, [ [ 0x1eb9 +  2, 0x2143 + 29 ],
                                                            [ 0x1eb9 + 11, 0x2170 + 11 ],
                                                            [ 0x1eb9 + 18, 0x20ba + 7 ],
                                                            [ 0x1eb9 + 25, 0x2143 + 40 ] ], 6),  // first, last exit unsure
    '6-3' => new LevelDefinition('6-3', 0x2d, 0x1fde, 0x2906, null, 6),
    '6-4' => new LevelDefinition('6-4', 0x60, 0x0000, 0x0000, null, 0),
    '7-1' => new LevelDefinition('7-1', 0x33, 0x209e, 0x2b8e, [ [ 0x209e +  4, 0x2143 + 8 ] ], 6),
    '7-2' => new LevelDefinition('7-2', 0x01, 0x0000, 0x0000, [ [ 0x2181 + 10, null ] ], 5),
    '7-3' => new LevelDefinition('7-3', 0x27, 0x0000, 0x0000, null, 7),
    '7-4' => new LevelDefinition('7-4', 0x64, 0x1e1a, 0x237f, null, 0),
    '8-1' => new LevelDefinition('8-1', 0x30, 0x200b, 0x2a0f, [ [ 0x200b + 6,  0x2143 + 16 ] ], 0),
    '8-2' => new LevelDefinition('8-2', 0x32, 0x2070, 0x2b15, [ [ 0x2070 + 26, 0x2143 + 43 ] ], 0),
    '8-3' => new LevelDefinition('8-3', 0x21, 0x1e8e, 0x24de, null, 0),
    '8-4' => new LevelDefinition('8-4', 0x65, 0x1e2f, 0x240a, null, 0),
    'Pipe Transition' =>   new LevelDefinition('Pipe Transition',   0x29, 0x0000, 0x0000, []), // what is enemy offset?
    'Cloud Area 1' =>      new LevelDefinition('cloud-area-1',      0x2b, 0x1fb0, 0x0000, []),
    'Cloud Area 2' =>      new LevelDefinition('cloud-area-2',      0x34, 0x20ba, 0x0000, []),
    'Water Area' =>        new LevelDefinition('water-area',        0x00, 0x2170, 0x0000, []),
    'Water Area 8-4' =>    new LevelDefinition('water-8-4',         0x02, 0x21ab, 0x0000, []),
    'Underground Bonus' => new LevelDefinition('underground-bonus', 0x42, 0x2143, 0x0000, []),  // pretty sure this is 0x42, but c2 also works.......
    '4-2 Warp Zone' =>     new LevelDefinition('4-2-warp-zone',     0x2f, 0x200a, 0x0000, []),
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