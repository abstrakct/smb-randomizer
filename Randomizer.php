<?php namespace SMBR;


/*
 * The main randomizer class!
 *
 * Again inspired by the ALttP Randomizer.
 *
 * Uses mt_rand like the ALttP Randomizer.
 *
 * Developed on php 7.2 - using 7.1 can produce different random numbers!
 * http://php.net/manual/en/migration72.incompatible.php#migration72.incompatible.rand-mt_rand-output
 */

// TODO: CHECK WARP ZONES!!!!!! Do they work?


use SMBR\Game;
use SMBR\Colorscheme;
use SMBR\Translator;
use SMBR\Enemy;
use SMBR\Levels;
use SMBR\Level;
use SMBR\Item;

function enemyIsInPool($o, $pool) {
    foreach ($pool as $p) {
        if ($o == $p->num) {
            return true;
        }
    }
    
    return false;
}

class Randomizer {
    public $flags;
    public $seedhash;
    protected $rng_seed;
    protected $seed;
    protected $options;
    protected $rom;
    private $level = [];
    private $trans;

    /**
     * Create a new randomizer.
     *
     * TODO: error checking etc.
     *
     * @param int seed seed to use for RNG
     * @param array opt options for randomization
     * @param Rom rom the rom object to modify
     *
     * @return void
     */
    public function __construct($seed = 1, $opt = null, $rom = null) {
        $this->rng_seed = $seed;
        $this->options = $opt;
        $this->rom = $rom;
        $this->trans = new Translator();
    }

    public function printOptions() {
        if ($this->options["webmode"])
            print("<br>*** OPTIONS ***<br>Seed: $this->rng_seed<br>");
        else
            print("\n\n*** OPTIONS ***\nSeed: $this->rng_seed\n");

        foreach ($this->options as $key => $value) {
            if ($this->options["webmode"]) {
                if ($key != "webmode")
                    print("$key: $value<br>");
            } else {
                print("$key: $value\n");
            }
        }
    }

    public function getSeed() {
        return $this->rng_seed;
    }

    public function setMarioColorScheme(string $colorscheme) : self {
        global $log, $colorschemes;
        $log->write("Mario Color Scheme: " . $colorscheme . "\n");
        if($colorscheme == "random") {
            $this->setSeed();
            $outer = mt_rand(0, 255);
            $skin = mt_rand(0, 255);
            $inner = mt_rand(0, 255);
        } else {
            $outer = $colorschemes[$colorscheme]->outer;
            $skin  = $colorschemes[$colorscheme]->skin;
            $inner = $colorschemes[$colorscheme]->inner;
        }
        $this->rom->setMarioInnerColor($inner);
        $this->rom->setMarioSkinColor($skin);
        $this->rom->setMarioOuterColor($outer);
        return $this;
    }

    public function setFireColorScheme(string $colorscheme) : self {
        global $log, $colorschemes;
        $log->write("Fire Mario/Luigi Color Scheme: " . $colorscheme . "\n");
        if($colorscheme == "random") {
            $this->setSeed();
            $outer = mt_rand(0, 255);
            $skin = mt_rand(0, 255);
            $inner = mt_rand(0, 255);
        } else {
            $outer = $colorschemes[$colorscheme]->outer;
            $skin  = $colorschemes[$colorscheme]->skin;
            $inner = $colorschemes[$colorscheme]->inner;
        }
        $this->rom->setFireInnerColor($inner);
        $this->rom->setFireSkinColor($skin);
        $this->rom->setFireOuterColor($outer);
        return $this;
    }

    public function setLuigiColorScheme(string $colorscheme) : self {
        global $log, $colorschemes;
        $log->write("Luigi Color Scheme: " . $colorscheme . "\n");
        if($colorscheme == "random") {
            $this->setSeed();
            $outer = mt_rand(0, 255);
            $skin = mt_rand(0, 255);
            $inner = mt_rand(0, 255);
        } else {
            $outer = $colorschemes[$colorscheme]->outer;
            $skin  = $colorschemes[$colorscheme]->skin;
            $inner = $colorschemes[$colorscheme]->inner;
        }
        $this->rom->setLuigiInnerColor($inner);
        $this->rom->setLuigiSkinColor($skin);
        $this->rom->setLuigiOuterColor($outer);
        return $this;
    }

        // TODO: store data in Game object and do the actual writing in rom->writeGame() ?!?!?!?!?
        // also improve this - we have enemy data offsets in the level data! 
    public function shuffleEnemiesOnLevel(string $level) {
        global $dont_randomize;
        global $full_enemy_pool;
        global $reasonable_enemy_pool;
        global $toad_pool;
        global $generator_pool;
        global $enemy_data_offsets_for_shuffling;
        global $enemy;
        global $log;

        $end = 0;
        $percentage = 100;  // if == 100 then all enemies will be randomized, if 50 there's a 50% chance of randomization happening for each enemy, etc.
        // TODO: change percentage based on settings/flags/something.
        // TODO: or remove this percentage setting?

        $data = $this->rom->read($enemy_data_offsets_for_shuffling[$level], 100);
        foreach ($data as $byte) {
            $end++;
            if($byte == 0xFF) {
                break;
            }
        }
        for($i = 0; $i < $end; $i+=2) {
            $do_randomize = true;
            $x = $data[$i] & 0xf0;
            $y = $data[$i] & 0x0f;
            if($y == 0xE) {
                $i++;
            } else if ($y > 0xE) {
                continue;
            } else {
                if($data[$i] != 0xFF) {
                    $p = $data[$i+1] & 0b10000000;
                    $h = $data[$i+1] & 0b01000000;
                    $o = $data[$i+1] & 0b00111111;  // this is the enemy
                    /* Some enemies can't be randomized, so let's check for those */
                    foreach($dont_randomize as $nope) {
                        if($o == $nope->num) {
                            $log->write("Found un-randomizable enemy object!\n");
                            $do_randomize = false;
                        }
                    }
                    if ($do_randomize) {
                        $newdata = 0;
                        if(mt_rand(1, 100) <= $percentage) {
                            if($o == $enemy['Toad']) {
                                $z = count($toad_pool);
                                $newo = $toad_pool[mt_rand(0, count($toad_pool) - 1)]->num;
                                $newcoord = 0x98;
                                $this->rom->write($enemy_data_offsets_for_shuffling[$level] + $i, pack('C*', $newcoord));
                            } else if ($o == $enemy['Bowser Fire Generator'] or $o == $enemy['Red Flying Cheep-Cheep Generator'] or $o == $enemy['Bullet Bill/Cheep-Cheep Generator']) {
                                $newo = $generator_pool[mt_rand(0, count($generator_pool) - 1)]->num;
                            } else {
                                $newo = $reasonable_enemy_pool[mt_rand(0, count($reasonable_enemy_pool) - 1)]->num;
                            }
                            //printf("i = %d data[%d] = %02x data[%d+1] = %02x newo = %02x\n", $i, $i, $data[$i], $i, $data[$i+1], $newo);
                            $newdata = (($data[$i+1] & 0b10000000) | ($data[$i+1] & 0b01000000)) | $newo;
                            //$data[$i+1] = $newdata;
                            //printf("i = %d  newdata: 0x%02x\n", $i, $newdata);
                            //print_r($newdata . "\n");
                            $this->rom->write($enemy_data_offsets_for_shuffling[$level] + $i + 1, pack('C*', $newdata));
                            //printf("x: %02x  y: %02x  p: %02x  h: %02x  o: %02x (%s)\n", $x, $y, $p, $h, $o, getenemyname($newo));
                        }
                    }
                }
            }
        }
    }

    public function shuffleEnemies() {
        global $enemy_data_offsets_for_shuffling;
        global $log;
        foreach ($enemy_data_offsets_for_shuffling as $key => $value) {
            $m = "Shuffling enemies on level " . $key . "\n";
            $log->write($m);
            $this->shuffleEnemiesOnLevel($key);
        }
    }

    public function shuffleEnemiesInPools(&$game) {
        global $dont_randomize;
        global $toad_pool, $generator_pool;
        global $koopa_pool, $goomba_pool, $firebar_pool, $lakitu_pool;
        global $enemy;
        global $log;

        $log->write("Shuffling enemies in pools!\n");
        $percentage = 100;  // if == 100 then all enemies will be randomized, if 50 there's a 50% chance of randomization happening for each enemy, etc.
        // TODO: change percentage based on settings/flags/something.
        // TODO: or remove this percentage setting?

        foreach ($game->worlds as $world) {
            foreach ($world->levels as $level) {
                $end = 0;
                if ($level->enemy_data_offset == 0x0000)
                    break;
                $data = $this->rom->read($level->enemy_data_offset, 100);
                foreach ($data as $byte) {
                    $end++;
                    if($byte == 0xFF) {
                        break;
                    }
                }

                for($i = 0; $i < $end; $i+=2) {
                    $do_randomize = true;
                    $x = $data[$i] & 0xf0;
                    $y = $data[$i] & 0x0f;
                    if($y == 0xE) {
                        $i++;
                    } else if ($y > 0xE) {
                        continue;
                    } else {
                        if($data[$i] != 0xFF) {
                            $p = $data[$i+1] & 0b10000000;
                            $h = $data[$i+1] & 0b01000000;
                            $o = $data[$i+1] & 0b00111111;  // this is the enemy

                            /* Some enemies can't be randomized, so let's check for those */
                            foreach($dont_randomize as $nope) {
                                if($o == $nope->num) {
                                    $log->write("Found un-randomizable enemy object!\n");
                                    $do_randomize = false;
                                }
                            }

                            if ($do_randomize) {
                                $newdata = 0;
                                if(mt_rand(1, 100) <= $percentage) {
                                    if($o == $enemy['Toad']) {
                                        $z = count($toad_pool);
                                        $newo = $toad_pool[mt_rand(0, count($toad_pool) - 1)]->num;
                                        $newcoord = 0x98;
                                        $this->rom->write($level->enemy_data_offset + $i, pack('C*', $newcoord));
                                    } else if (enemyIsInPool($o, $generator_pool)) {
                                        $newo = $generator_pool[mt_rand(0, count($generator_pool) - 1)]->num;
                                    } else if (enemyIsInPool($o, $goomba_pool)) {
                                        $newo = $goomba_pool[mt_rand(0, count($goomba_pool) - 1)]->num;
                                    } else if (enemyIsInPool($o, $koopa_pool)) {
                                        $newo = $koopa_pool[mt_rand(0, count($koopa_pool) - 1)]->num;
                                    } else if (enemyIsInPool($o, $firebar_pool)) {
                                        $newo = $firebar_pool[mt_rand(0, count($firebar_pool) - 1)]->num;
                                    } else if ($o == $enemy["Lakitu"]) {
                                        $newo = $lakitu_pool[mt_rand(0, count($lakitu_pool) - 1)]->num;
                                    }


                                    $newdata = (($data[$i+1] & 0b10000000) | ($data[$i+1] & 0b01000000)) | $newo;
                                    $this->rom->write($level->enemy_data_offset + $i + 1, pack('C*', $newdata));
                                }
                            }
                        }
                    }
                }
            }
        }
    }

    public function randomizeBlocks(Game &$game, $frompool, $topool) {
        global $log;
        $log->write("Randomizing blocks!\n");

        // 0xFD signifies end of level object data!
        foreach ($game->worlds as $world) {
            foreach ($world->levels as $level) {
                if ($level->level_data_offset == 0x0000)
                    break;
                $end = 0;
                //printf("0x%02x\n", $level->level_data_offset);
                $data = $this->rom->read($level->level_data_offset, 200);
                foreach ($data as $byte) {
                    $end++;
                    if($byte == 0xFD) {
                        break;
                    }
                }

                for($i = 2; $i < $end; $i+=2) {
                    $do_randomize = true;
                    $y = $data[$i] & 0b00001111;
                    if ($y > 0x0B)
                        $do_randomize = false;

                    if ($do_randomize) {
                        $p = $data[$i+1] & 0b10000000;
                        $object = $data[$i+1] & 0b01111111;
                        $newdata = 0x99;
                        // change to add write data to Game object
                        if (in_array($object, $frompool))  {
                            $pull_key = mt_rand(0, count($topool) - 1);
                            $newdata = $topool[$pull_key];
                            $new_object = $p | $newdata;
                            $this->rom->write($level->level_data_offset + $i + 1, pack('C*', $new_object));
                        }
                    }
                }
            }
        }
    }

    /*
     * Shuffle levels, but castles can appear anywhere, except 8-4 which is always last.
     * Each castle represents the end of a world, but currently there are no restrictions on how
     * many levels can be in a world, except that there will be no more than 32 levels total like in vanilla.
     */
    public function shuffleAllLevels(&$game) {
        global $log;
        global $vanilla_level;
        $all_levels = [ '1-1', '1-2', '1-3', '1-4', '2-1', '2-2', '2-3', '2-4', '3-1', '3-2', '3-3', '3-4', '4-1', '4-2', '4-3', '4-4', '5-1', '5-2', '5-3', '5-4', '6-1', '6-2', '6-3', '6-4', '7-1', '7-2', '7-3', '7-4', '8-1', '8-2', '8-3' ];

        $log->write("Shuffling ALL levels\n");
        $shuffledlevels = mt_shuffle($all_levels);
        //print_r($shuffledlevels);

        $lastlevelindex = 0;
        $levelindex = 1;
        $worldindex = 1;
        $shuffleindex = 0;

        // TODO: reduce code duplication!
        if ($this->options['Pipe Transitions'] == 'remove') {
            for ($i = 0; $i < count($shuffledlevels); $i++) {
                $game->worlds[$worldindex]->levels[$levelindex] = $vanilla_level[$shuffledlevels[$shuffleindex]];
                if ($vanilla_level[$shuffledlevels[$shuffleindex]]->map >= 0x60 and $vanilla_level[$shuffledlevels[$shuffleindex]]->map <= 0x65) {
                    // it's a castle, so increase the world index and reset the level index
                    $worldindex++;
                    if ($worldindex > 8)
                        $worldindex = 8;
                    $levelindex = 0;
                }
                $lastlevelindex = $levelindex;
                $levelindex++;
                $shuffleindex++;
            }
            $game->worlds[8]->levels[$lastlevelindex+1] = $vanilla_level['8-4'];
        } else if ($this->options['Pipe Transitions'] == 'keep') {
            for ($i = 0; $i < count($shuffledlevels); $i++) {
                $game->worlds[$worldindex]->levels[$levelindex] = $vanilla_level[$shuffledlevels[$shuffleindex]];
                $levelindex++;

                if ($vanilla_level[$shuffledlevels[$shuffleindex]]->map >= 0x60 and $vanilla_level[$shuffledlevels[$shuffleindex]]->map <= 0x65) {
                    // it's a castle, so increase the world index and reset the level index
                    $worldindex++;
                    if ($worldindex > 8)
                        $worldindex = 8;
                    $levelindex = 0;
                }

                if($shuffleindex < 30) {
                    if (in_array($vanilla_level[$shuffledlevels[$shuffleindex+1]]->map, [ $vanilla_level['1-2']->map, $vanilla_level['2-2']->map, $vanilla_level['4-2']->map ])) {
                        $game->worlds[$worldindex]->levels[$levelindex] = $vanilla_level['Pipe Transition'];
                    }
                }

                $levelindex++;
                $shuffleindex++;
            }
            $lastlevelindex  = count($game->worlds[8]->levels) + 2;
            $game->worlds[8]->levels[$lastlevelindex] = $vanilla_level['8-4'];
        }
    }
    /*
     * Shuffle levels, but make sure each -4 is a castle.
     * Castles are also shuffled, except the 8-4 which is 8-4
     * TODO: add keeping pipe transitions in place.
     */
    public function shuffleLevelsWithNormalWorldLength(&$game) {
        global $log;
        global $vanilla_level;
        $levels = [ '1-1', '1-2', '1-3', '2-1', '2-2', '2-3', '3-1', '3-2', '3-3', '4-1', '4-2', '4-3', '5-1', '5-2', '5-3', '6-1', '6-2', '6-3', '7-1', '7-2', '7-3', '8-1', '8-2', '8-3' ];
        $castles = [ '1-4', '2-4', '3-4', '4-4', '5-4', '6-4', '7-4' ];

        $log->write("Shuffling levels (normal world length)\n");

        $shuffledlevels  = mt_shuffle($levels);
        $shuffledcastles = mt_shuffle($castles);

        if($this->options['Pipe Transitions'] == 'remove') {
            $log->write("Removing pipe transitions\n");
            $levelindex = 0;
            $castleindex = 0;
            for ($w = 1; $w <= 8; $w++) {
                for ($i = 0; $i < 3; $i++) {
                    $game->worlds[$w]->levels[$i] = $vanilla_level[$shuffledlevels[$levelindex]];
                    $levelindex++;
                }
                if($castleindex < 7)
                    $game->worlds[$w]->levels[3] = $vanilla_level[$shuffledcastles[$castleindex]];
                $castleindex++;
            }
            $game->worlds[8]->levels[3] = $vanilla_level['8-4'];
        } else if($this->options['Pipe Transitions'] == 'keep') {
            // TODO: implement this. 
            // Probably needs a better structure where we can insert a level in between others!
            // fixMidwayPoints must also be changed for this to work.
            // - or maybe fix midway points before inserting pipe transitions
            print("Sorry, normal world length + keeping pipe transitions is NOT IMPLEMENTED YET!\n\n");
            exit(0);
            
            //$levelindex = 0;
            //$castleindex = 0;
            //for ($w = 1; $w <= 8; $w++) {
            //    for ($i = 0; $i < 3; $i++) {
            //        $game->worlds[$w]->levels[$i] = $vanilla_level[$shuffledlevels[$levelindex]];
            //        $levelindex++;
            //    }
            //    if($castleindex < 7)
            //        $game->worlds[$w]->levels[3] = $vanilla_level[$shuffledcastles[$castleindex]];
            //    $castleindex++;
            //}
            //$game->worlds[8]->levels[3] = $vanilla_level['8-4'];

            //for ($w = 1; $w <= 8; $w++) {
            //    for ($l = 0; $l < 3; $l++) {
            //        if (in_array($game->worlds[$w]->levels[$l]->map, [ $vanilla_level['1-2']->map, $vanilla_level['2-2']->map, $vanilla_level['4-2']->map ])) {
            //            $position = $l - 1;
            //            if ($position < 0)
            //                $position = 0;
            //            array_splice($game->worlds[$w]->levels, $position, 0, [ $vanilla_level['Pipe Transition'] ]);
            //        }
            //    }
            //}




            //$log->write("Keeping pipe transitions\n");
            //$levelindex = 0;
            //$shuffleindex = 0;
            //$castleindex = 0;
            //for ($w = 1; $w <= 8; $w++) {
            //    while (!in_array($vanilla_level[$shuffledlevels[$shuffleindex]]->map, [ 0x60, 0x61, 0x62, 0x63, 0x64, 0x65 ])) {
            //        if (in_array($vanilla_level[$shuffledlevels[$shuffleindex]]->map, [ $vanilla_level['1-2']->map, $vanilla_level['2-2']->map, $vanilla_level['4-2']->map ])) {
            //            $game->worlds[$w]->levels[$levelindex] = $vanilla_level['Pipe Transition'];
            //            $levelindex++;
            //        }

            //        $game->worlds[$w]->levels[$levelindex] = $vanilla_level[$shuffledlevels[$shuffleindex]];

            //        $levelindex++;
            //        $shuffleindex++;
            //    }

            //    $levelindex = 0;

            //    if($castleindex < 7)
            //        $game->worlds[$w]->levels[$levelindex] = $vanilla_level[$shuffledcastles[$castleindex]];
            //    $castleindex++;
            //}
            //$game->worlds[8]->levels[4] = $vanilla_level['8-4'];
            
        }
    }

    public function fixPipes(Game &$game) {
        // TODO: store data in Game object and do the actual writing in rom->writeGame() ?!?!?!?!?
        global $log;
        $levels = ['4-1', '1-2', '2-1', '1-1', '3-1', '4-1', '4-2', '5-1', '5-2', '6-2', '7-1', '8-1', '8-2', '2-2', '7-2'];
        $log->write("Fixing Pipes\n");
        foreach ($game->worlds as $world) {
            foreach ($world->levels as $level) {
                if (in_array($level->name, $levels)) {
                    if ($level->pipe_pointers) {
                        foreach ($level->pipe_pointers as list($entry, $exit)) {
                            $log->write("Fixing pipe in " . $level->name . " - new world is " . $world->num . "\n");
                            $new_world = $world->num - 1;

                            // entry
                            if ($entry != null) {
                                $entry_data = $this->rom->read($entry, 1);
                                $new_entry_data = (($new_world << 5) | ($entry_data & 0b00011111));
                                $this->rom->write($entry, pack('C*', $new_entry_data));
                            }
                            // exit
                            if ($exit != null) {
                                $exit_data = $this->rom->read($exit, 1);
                                $new_exit_data =  (($new_world << 5) | ($exit_data  & 0b00011111));
                                $this->rom->write($exit, pack('C*', $new_exit_data));
                            }
                        }
                    }
                }
            }
        }
    }

    public function fixMidwayPoints(Game &$game) {
        global $log;
        $log->write("Fixing midway points:\n");

        if ($this->options['Shuffle Levels'] == 'true' and $this->options['Normal World Length'] == 'false') {
            // Remove midway points
            $log->write("Removing all midway points!\n");
            for ($i = 0; $i < 0xF; $i++) {
                $game->midway_points[$i] = 0x00;
            }
        }

        if ($this->options['Shuffle Levels'] == 'true' and $this->options['Normal World Length'] == 'true') {
            // Fix midway points
            $log->write("Moving midway points around to correct positions!\n");
            $mpindex = 0;
            foreach ($game->worlds as $world) {
                $game->midway_points[$mpindex] = ($world->levels[0]->midway_point << 4) | ($world->levels[1]->midway_point);
                $mpindex++;
                $game->midway_points[$mpindex] = ($world->levels[2]->midway_point << 4) | ($world->levels[3]->midway_point);
                $mpindex++;
            }
        }
    }

    public function setTextRando() {
        global $log;
        $log->write("Changing Texts\n");
        $this->rom->write(0x09fb5, pack('C*', $this->trans->asciitosmb('R')));
        $this->rom->write(0x09fb6, pack('C*', $this->trans->asciitosmb('A')));
        $this->rom->write(0x09fb7, pack('C*', $this->trans->asciitosmb('N')));
        $this->rom->write(0x09fb8, pack('C*', $this->trans->asciitosmb('D')));
        $this->rom->write(0x09fb9, pack('C*', $this->trans->asciitosmb('O')));
        $this->rom->write(0x09fba, pack('C*', $this->trans->asciitosmb('×')));
    }

    public function setTextSeedhash(string $text) {
        /*
         * Replace "NINTENDO" in "(C)1985 NINTENDO" on the title screen with the first 8 characters of the seedhash
         * TODO: see if there's a way to draw some sprites instead!
         */
        for($i = 0; $i < 8; $i++) {
            $this->rom->write(0x09fbb + $i, pack('C*', $this->trans->asciitosmb($text[$i])));
        }
    }

    public function getFlags() {
        return implode("", $this->flags);
    }

    public function makeFlags() {
        $this->flags[0] = $this->options['Pipe Transitions'][2];
        $this->flags[1] = $this->options['Shuffle Levels'][0];
        $this->flags[2] = $this->options['Normal World Length'][1];
        $this->flags[3] = $this->options['Shuffle Enemies'][1];
        $this->flags[4] = $this->options['Shuffle Blocks'][2];
        $s = implode("", $this->flags);
        $f = strtoupper($s);
        if ($this->options["webmode"])
            print("Flags: $f <br>");
        else
            print("Flags: $f\n");
        $this->makeSeedHash();
    }

    public function makeSeedHash() {
        global $smbr_version;
        $hashstring = implode("", $this->flags) . strval($this->getSeed() . $smbr_version . $this->rom->getMD5());
        $this->seedhash = hash("crc32b", $hashstring);
        //print("makkSeedHash()\n
        //          md5: " . hash("md5", $hashstring) . "\n
        //          crc: " . $this->seedhash . "\n
        //       md5crc: " . hash("crc32b", hash("md5", $hashstring)) . "\n");

        if ($this->options["webmode"])
            print("SeedHash: $this->seedhash <br>");
        else
            print("SeedHash: $this->seedhash\n");
    }

    public function getSeedHash() {
        return $this->seedhash;
    }

    public function setSeed(int $rng_seed = null) {
        $rng_seed = $rng_seed ?: random_int(1, 999999999); // cryptographic pRNG for seeding
		$this->rng_seed = $rng_seed % 1000000000;
		mt_srand($this->rng_seed);
    }

    // Here we go!
    public function makeSeed() {
        global $log;

        $game = new Game();

        if ($this->options["webmode"])
            print("<br>Here we go! Making randomized SMB ROM with seed $this->rng_seed <br>");
        else
            print("\nHere we go! Making randomized SMB ROM with seed $this->rng_seed\n");

        //  Shuffle Levels
        if ($this->options['Shuffle Levels'] == "true") {
            if ($this->options['Normal World Length'] == "true") {
                $this->shuffleLevelsWithNormalWorldLength($game);
            } else {
                $this->shuffleAllLevels($game);
            }
        } else {
            $game->setVanilla();
        }

        //  Shuffle Enemies
        if ($this->options['Shuffle Enemies'] == "full") {
            $this->shuffleEnemies();
        } else if ($this->options['Shuffle Enemies'] == "pools") {
            $this->shuffleEnemiesInPools($game);
        }

        // Shuffle Blocks
        if ($this->options['Shuffle Blocks'] == "all") {
            global $all_items;
            $this->randomizeBlocks($game, $all_items, $all_items);
        } else if ($this->options['Shuffle Blocks'] == "powerups") {
            global $powerups;
            $this->randomizeBlocks($game, $powerups, $powerups);
        } else if ($this->options['Shuffle Blocks'] == "grouped") {
            global $all_question_blocks, $all_hidden_blocks, $all_brick_blocks;
            $this->randomizeBlocks($game, $all_question_blocks, $all_question_blocks);
            $this->randomizeBlocks($game, $all_hidden_blocks, $all_hidden_blocks);
            $this->randomizeBlocks($game, $all_brick_blocks, $all_brick_blocks);
        } else if ($this->options['Shuffle Blocks'] == "coins") {
            global $all_items, $all_coins;
            $this->randomizeBlocks($game, $all_items, $all_coins);
        } else if ($this->options['Shuffle Blocks'] == "none") {
            $log->write("No randomization of blocks!\n");
        }

        // Fix Pipes
        $this->fixPipes($game);

        // Fix Midway Points
        $this->fixMidwayPoints($game);

        // Set texts
        $this->setTextRando();
        $this->setTextSeedhash($this->seedhash);

        // Set colorschemes
        $this->setMarioColorScheme($this->options['Mario Color Scheme']);
        $this->setLuigiColorScheme($this->options['Luigi Color Scheme']);
        $this->setFireColorScheme($this->options['Fire Color Scheme']);

        return $game;
    }
}

// Shuffle the contents of an array.
// Taken from ALttP Randomizer
function mt_shuffle(array $array) {
    $new_array = [];
    while(count($array)) {
        $pull_key = mt_rand(0, count($array) - 1);
        $new_array = array_merge($new_array, array_splice($array, $pull_key, 1));
    }

    return $new_array;
}


