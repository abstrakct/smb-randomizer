<?php

// TODO: search ROM for leveldata instead of using hardcoded offsets/numbers?!

$vanillamap = [
    '1-1' => 0x25,
    '1-1 to 1-2' => 0x29,
    '1-2' => 0xc0,
    '1-3' => 0x26,
    '1-4' => 0x60,
    
    '2-1' => 0x28,
    '2-1 to 2-2' => 0x29,
    '2-2' => 0x01,
    '2-3' => 0x27,
    '2-4' => 0x62,
    
    '3-1' => 0x24,
    '3-2' => 0x35,
    '3-3' => 0x20,
    '3-4' => 0x63,
    
    '4-1' => 0x22,
    '4-1 to 4-2' => 0x29,
    '4-2' => 0x41,
    '4-3' => 0x2c,
    '4-4' => 0x61,
    
    '5-1' => 0x2a,
    '5-2' => 0x31,
    '5-3' => 0x26,
    '5-4' => 0x62,
    
    '6-1' => 0x2e,
    '6-2' => 0x23,
    '6-3' => 0x2d,
    '6-4' => 0x60,
    
    '7-1' => 0x33,
    '7-1 to 7-2' => 0x29,
    '7-2' => 0x01,
    '7-3' => 0x27,
    '7-4' => 0x64,
    
    '8-1' => 0x30,
    '8-2' => 0x32,
    '8-3' => 0x21,
    '8-4' => 0x65,
];

$map[1][1] = 0x25;
// pipe transition
$map[1][2] = 0xc0;
$map[1][3] = 0x26;
$map[1][4] = 0x60;

$map[2][1] = 0x28;
// pipe transition
$map[2][2] = 0x01;
$map[2][3] = 0x27;
$map[2][4] = 0x62;

$map[3][1] = 0x24;
$map[3][2] = 0x35;
$map[3][3] = 0x20;
$map[3][4] = 0x63;

$map[4][1] = 0x22;
// pipe transition
$map[4][2] = 0x41;
$map[4][3] = 0x2c;
$map[4][4] = 0x61;

$map[5][1] = 0x2a;
$map[5][2] = 0x31;
$map[5][3] = 0x26;
$map[5][4] = 0x62;

$map[6][1] = 0x2e;
$map[6][2] = 0x23;
$map[6][3] = 0x2d;
$map[6][4] = 0x60;

$map[7][1] = 0x33;
// pipe transition
$map[7][2] = 0x01;
$map[7][3] = 0x27;
$map[7][4] = 0x64;

$map[8][1] = 0x30;
$map[8][2] = 0x32;
$map[8][3] = 0x21;
$map[8][4] = 0x65;

$all_levels = 
    [ 0x25, 0xc0, 0x26, 0x60,
      0x28, 0x01, 0x27, 0x62,
      0x24, 0x35, 0x20, 0x63,
      0x22, 0x41, 0x2c, 0x61,
      0x2a, 0x31, 0x26, 0x62,
      0x2e, 0x23, 0x2d, 0x60,
      0x33, 0x01, 0x27, 0x64,
      0x30, 0x32, 0x21, 0x65 ];

$levels = 
    [ 0x25, 0xc0, 0x26,
      0x28, 0x01, 0x27,
      0x24, 0x35, 0x20,
      0x22, 0x41, 0x2c,
      0x2a, 0x31, 0x26,
      0x2e, 0x23, 0x2d,
      0x33, 0x01, 0x27,
      0x30, 0x32, 0x21 ];

$castles = [ 0x60, 0x62, 0x63, 0x61, 0x62, 0x60, 0x64 ]; // 65 is final castle, maybe add option to include it in the pool?

$enemydataoffsets = [
    '1-4' => 0x1d80,
    '6-4' => 0x1d80,
    '4-4' => 0x1da7,
    '2-4' => 0x1dc0,
    '5-4' => 0x1dc0,
    '3-4' => 0x1def,
    '7-4' => 0x1e1a,
    '8-4' => 0x1e2f,
    '3-3' => 0x1e69,
    '8-3' => 0x1e8e,
    '4-1' => 0x1eab,
    '6-2' => 0x1eb9,
    '3-1' => 0x1ee0,
    '1-1' => 0x1f11,
    '1-3' => 0x1f2f,
    '5-3' => 0x1f2f,
    '2-3' => 0x1f4c,
    '7-3' => 0x1f4c,
    '2-1' => 0x1f61,
    '5-1' => 0x1f8c,
    '4-3' => 0x1fb9,
    '6-3' => 0x1fde,
    '6-1' => 0x2001,
    '8-1' => 0x200b,
    '5-2' => 0x2045,
    '8-2' => 0x2070,
    '7-1' => 0x209e,
    '3-2' => 0x20c3,
    '1-2' => 0x20e8,
    '4-2' => 0x2115,
    '2-2' => 0x2181,
    '7-2' => 0x2181,
    '2-1-cloud' => 0x1fb0,
    '3-1-cloud' => 0x20ba,
    '5-2-cloud' => 0x1fb0,
    '6-2-cloud' => 0x20ba,
    '5-2-water' => 0x2170,
    '6-2-water' => 0x2170,
    '8-4-water' => 0x21ab,
    'underground-bonus' => 0x2143,
];

// add lost levels?

// the "pipe transition level" that normally comes after maps 25, 28, 22
$map_pipetransition = 0x29;


$leveloffset[1][1] = 0x269e;
$levellength[1][1] = 0x2702 - $leveloffset[1][1] + 1;
$leveloffset[1][2] = 0x269e;
$levellength[1][2] = 0x2702 - $leveloffset[1][2] + 1;

$enemiesoffset[1][1] = 0x1f11;
$enemieslength[1][1] = 0x1f2e - $enemiesoffset[1][1] + 1;

if(!session_id()) session_start();
if(!isset($_SESSION['levels'])) $_SESSION['levels'] = $levels;
if(!isset($_SESSION['castles'])) $_SESSION['castles'] = $castles;
if(!isset($_SESSION['all_levels'])) $_SESSION['all_levels'] = $all_levels;
if(!isset($_SESSION['enemydataoffsets'])) $_SESSION['enemydataoffsets'] = $enemydataoffsets;

?>
