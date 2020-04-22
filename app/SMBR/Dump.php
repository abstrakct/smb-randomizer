<?php 

namespace App\SMBR;

// TODO: Fix everything here!!

function getlevelobjectname(int $num): string
{
    $levelobjectname = ["? block mushroom", "? block coin", "hidden block coin", "hidden block 1up", "brick mushroom", "brick beanstalk", "brick star", "brick multicoin", "brick 1up", "sideways pipe", "used block", "trampoline", "reverse L pipe", "flag pole", "bowser bridge", "nothing"];
    if ($num < 0x10) {
        return $levelobjectname[$num];
    } else if ($num < 0x20) {
        return "island/cannon";
    } else if ($num < 0x30) {
        return "horizontal brick";
    } else if ($num < 0x40) {
        return "horizontal block";
    } else if ($num < 0x50) {
        return "horizontal coins";
    } else if ($num < 0x60) {
        return "vertical brick";
    } else if ($num < 0x70) {
        return "horizontal block";
    } else if ($num < 0x78) {
        return "pipe";
    } else if ($num < 0x80) {
        return "pipe player can enter";
    } else {
        return "unknown!";
    }

}

function getenemyname(int $num): string
{
    $enemyname = ["green koopa", "red koopa walks off floors", "buzzy beetle", "red koopa stays on floors", "green koopa doesnt move", "hammer bro", "goomba", "blooper", "bullet bill", "yellow koopa paratroopa doesnt move", "green cheep cheep slow", "red cheep cheep fast", "podoboo", "pirhana plant", "green koopa paratroopa leaping", "red koopa down up", "green koopa left right", "lakitu", "spiny", "DONOTUSE", "red flying cheep cheep generator", "bowser fire generator", "fireworks generator", "bullet bill/cheep cheep generator", "DONOTUSE", "DONOTUSE", "DONOTUSE", "fire bar clockwise", "fast fire bar clockwise", "fire bar counterclockwise", "fast fire bar counterclockwise", "long fire bar clockwise", "DONOTUSE", "DONOTUSE", "DONOTUSE", "DONOTUSE", "left balance", "lift down up", "lift up", "lift down", "lift left right", "lift fall", "lift right", "short lift up", "short lift down", "BOWSER", "DONOTUSE", "DONOTUSE", "DONOTUSE", "DONOTUSE", "DONOTUSE", "DONOTUSE", "warp zone", "toad", "DONOTUSE", "2 goombas v-10", "3 goombas v-10", "2 goombas v-6", "3 goombas v-6", "2 green koopas v-10", "3 green koopas v-10", "2 green koopas v-6", "3 green koopas v-6", "DONOTUSE"];
    if ($num <= 0x3F) {
        return $enemyname[$num];
    } else {
        return "UNKNOWN!!";
    }

}

function levelobjectdump($leveloffset, $levellength, $rom)
{
    $area = $rom->read($leveloffset, $levellength);
    for ($i = 0; $i < $levellength; $i += 2) {
        if ($i == 0 or $i == 1) {
            continue;
        }
        // skip header
        // TODO: look for end of level marker etc.

        $x = $area[$i] & 0xf0;
        $y = $area[$i] & 0x0f;
        $p = $area[$i + 1] & 0x80;
        $o = $area[$i + 1] & 0x7f;

        printf("x: %02x  y: %02x  p: %02x  o: %02x (%s)\n", $x, $y, $p, $o, getlevelobjectname($o));
    }
}

function dumpLevelEnemies($leveloffset, $rom)
{
    print("\n ENEMY DATA DUMP \n");
    if ($leveloffset == null) {
        return;
    }

    $area = $rom->read($leveloffset, 1000);
    for ($i = 0; $i < 1000; $i += 2) {
        if ($area[$i] == 0xFF) {
            print("END OF LEVEL ENEMY DATA\n\n");
            break;
        }
        $x = $area[$i] & 0xf0;
        $y = $area[$i] & 0x0f;
        if ($y == 0xE) {
            print("  pipepointer - offset = $i\n");
            $byte1 = $area[$i];
            $byte2 = $area[$i + 1];
            $byte3 = $area[$i + 2];
            printf("  byte1: %02x byte2: %02x byte3: %02x\n", $byte1, $byte2, $byte3);
            printf("  map pointer:  %02x\n", $byte2 & 0x7f);
            printf("  active world: %01d\n", (($byte3 & 0xe0) >> 5) + 1);
            $i++;
        } else if ($y > 0x0e) {
            continue;
        } else {
            if ($area[$i] != 0xFF) {
                $p = $area[$i + 1] & 0x80;
                $h = $area[$i + 1] & 0x40;
                $o = $area[$i + 1] & 0x3f;
                printf("x: %02x  y: %02x  p: %02x  h: %02x  o: %02x (%s)\n", $x, $y, $p, $h, $o, getenemyname($o));
            }
        }
    }
}

// TODO: support various world lengths
function dumpRomInfoActualOrder($rom)
{
    global $vanilla_level;
    $data = $rom->read(0x1ccc, 32);
    for ($world = 0; $world < 8; $world++) {
        for ($level = 0; $level < 4; $level++) {
            $i = ($world * 4) + $level;
            $levelname = mapToName($data[$i]);
            printf("Dumping %d-%d (vanilla %s) (map = %02x)\n", $world + 1, $level + 1, $levelname, $data[$i]);
            dumpLevelEnemies($vanilla_level[$levelname]->enemy_data_offset, $rom);
        }
    }
}

function dumpRomInfoVanillaOrder($rom)
{
    global $vanilla_level;
    foreach ($vanilla_level as $l) {
        printf("Dumping vanilla %s (map = %02x)\n", $l->name, $l->map);
        dumpLevelEnemies($l->enemy_data_offset, $rom);
    }
}
