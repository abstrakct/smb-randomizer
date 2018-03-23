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

// TODO: SEPARATE RNG FOR COLORSCHEME! --- or --- do the colors last
//include "levels.php";

use SMBR\Game;
use SMBR\Colorscheme;
use SMBR\Translator;
use SMBR\Enemy;
use SMBR\Levels;
use SMBR\Level;

class Randomizer {
    public $flags;
    public $seedhash;
    public $colorschemes;
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
        $this->colorschemes = [
            'random' => new Colorscheme(0, 0, 0),
            'Mario' => new Colorscheme(0x16, 0x27, 0x18),
            'Luigi' => new Colorscheme(0x30, 0x27, 0x19),
            'Vanilla Fire' => new Colorscheme(0x37, 0x27, 0x16),
            'Pale Ninja' => new Colorscheme(0xce, 0xd0, 0x1e),
            'All Black' => new Colorscheme(0x8d, 0x8d, 0x8d),
            'Black & Blue' => new Colorscheme(0xcc, 0x18, 0x2f),
        ];
    }

    public function outputOptions() {
        print("\n\n*** SETTINGS ***\nSeed: $this->rng_seed\n");

        foreach ($this->options as $key => $value) {
            print("$key: $value\n");
        }
    }

    public function getSeed() {
        return $this->rng_seed;
    }

    public function setMarioColorScheme(string $colorscheme) : self {
        global $log;
        $log->write("Mario Color Scheme: " . $colorscheme . "\n");
        if($colorscheme == "random") {
            // make colors random, independent of the game seed
            $this->setSeed();
            $outer = mt_rand(0, 255);
            $skin = mt_rand(0, 255);
            $inner = mt_rand(0, 255);
        } else {
            $outer = $this->colorschemes[$colorscheme]->outer;
            $skin  = $this->colorschemes[$colorscheme]->skin;
            $inner = $this->colorschemes[$colorscheme]->inner;
        }
        $this->rom->setMarioInnerColor($inner);
        $this->rom->setMarioSkinColor($skin);
        $this->rom->setMarioOuterColor($outer);
        return $this;
    }

    public function setFireColorScheme(string $colorscheme) : self {
        global $log;
        $log->write("Fire Mario/Luigi Color Scheme: " . $colorscheme . "\n");
        if($colorscheme == "random") {
            $this->setSeed();
            $outer = mt_rand(0, 255);
            $skin = mt_rand(0, 255);
            $inner = mt_rand(0, 255);
        } else {
            $outer = $this->colorschemes[$colorscheme]->outer;
            $skin  = $this->colorschemes[$colorscheme]->skin;
            $inner = $this->colorschemes[$colorscheme]->inner;
        }
        $this->rom->setFireInnerColor($inner);
        $this->rom->setFireSkinColor($skin);
        $this->rom->setFireOuterColor($outer);
        return $this;
    }

    public function setLuigiColorScheme(string $colorscheme) : self {
        global $log;
        $log->write("Luigi Color Scheme: " . $colorscheme . "\n");
        if($colorscheme == "random") {
            $this->setSeed();
            $outer = mt_rand(0, 255);
            $skin = mt_rand(0, 255);
            $inner = mt_rand(0, 255);
        } else {
            $outer = $this->colorschemes[$colorscheme]->outer;
            $skin  = $this->colorschemes[$colorscheme]->skin;
            $inner = $this->colorschemes[$colorscheme]->inner;
        }
        $this->rom->setLuigiInnerColor($inner);
        $this->rom->setLuigiSkinColor($skin);
        $this->rom->setLuigiOuterColor($outer);
        return $this;
    }

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

    /*
     * Shuffle levels, but castles can appear anywhere, except 8-4 which is 8-4
     * Each castle represents the end of a world, but currently there are no restrictions on how
     * many levels can be in a world, except that there will be no more than 32 levels total like in vanilla.
     */
    public function shuffleAllLevels(&$game) {
        global $log;
        global $vanilla_level;
        $all_levels = [ '1-1', '1-2', '1-3', '1-4', '2-1', '2-2', '2-3', '2-4', '3-1', '3-2', '3-3', '3-4', '4-1', '4-2', '4-3', '4-4', '5-1', '5-2', '5-3', '5-4', '6-1', '6-2', '6-3', '6-4', '7-1', '7-2', '7-3', '7-4', '8-1', '8-2', '8-3' ];

        $log->write("Shuffling ALL levels\n");
        $shuffledlevels = mt_shuffle($all_levels);
        print_r($shuffledlevels);

        $lastlevelindex = 0;
        $levelindex = 1;
        $worldindex = 1;
        $shuffleindex = 0;

        // TODO: reduce code duplication!
        if ($this->options['Pipe Transitions'] == 'remove') {
            for ($i = 0; $i < count($shuffledlevels); $i++) {
                $game->worlds[$worldindex]->levels[$levelindex] = $vanilla_level[$shuffledlevels[$shuffleindex]];
                if ($vanilla_level[$shuffledlevels[$shuffleindex]]->map >= 0x60) {
                    // it's a castle, so increase the world index and reset the level index
                    $worldindex++;
                    if ($worldindex > 8)
                        $worldindex = 8;
                    $lastlevelindex = $levelindex;
                    $levelindex = 0;
                }
                $levelindex++;
                $shuffleindex++;
            }
            $game->worlds[8]->levels[$lastlevelindex+2] = $vanilla_level['8-4'];
            print_r($game);
        } else if ($this->options['Pipe Transitions'] == 'keep') {
            for ($i = 0; $i < count($shuffledlevels); $i++) {
                $game->worlds[$worldindex]->levels[$levelindex] = $vanilla_level[$shuffledlevels[$shuffleindex]];
                $levelindex++;

                if ($vanilla_level[$shuffledlevels[$shuffleindex]]->map >= 0x60 and $vanilla_level[$shuffledlevels[$shuffleindex]]->map <= 0x65) {
                    // it's a castle, so increase the world index and reset the level index
                    $worldindex++;
                    if ($worldindex > 8) $worldindex = 8;
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
            print_r($game);
        }
    }
    /*
     * Shuffle levels, but make sure each -4 is a castle.
     * Castles are also shuffled, except the 8-4 which is 8-4
     */
    public function shuffleLevelsWithCastlesLast(&$game) {
        global $log;
        global $vanilla_level;
        $levels = [ '1-1', '1-2', '1-3', '2-1', '2-2', '2-3', '3-1', '3-2', '3-3', '4-1', '4-2', '4-3', '5-1', '5-2', '5-3', '6-1', '6-2', '6-3', '7-1', '7-2', '7-3', '8-1', '8-2', '8-3' ];
        $castles = [ '1-4', '2-4', '3-4', '4-4', '5-4', '6-4', '7-4' ];

        $log->write("Shuffling levels (castles last)\n");

        $shuffledlevels  = mt_shuffle($levels);
        $shuffledcastles = mt_shuffle($castles);

        $levelindex = 0;
        $castleindex = 0;

        if($this->options['Pipe Transitions'] == 'remove') {
            for ($w = 1; $w <= 8; $w++) {
                for ($i = 0; $i < 3; $i++) {
                    $game->worlds[$w]->levels[$i] = $vanilla_level[$shuffledlevels[$levelindex]];
                    $levelindex++;
                }
                $game->worlds[$w]->levels[3] = $vanilla_level[$shuffledcastles[$castleindex]];
                $castleindex++;
            }
            $game->worlds[8]->levels[3] = $vanilla_level['8-4'];
            print_r($game);
        } else if($this->options['Pipe Transitions'] == 'keep') {
            print("shuffle levels, castles last, keep pipe transitions NOT IMPLEMENTED!\n");
        }
    }

    public function fixPipes(Game &$game) {
        global $log;
        //$levels = ['4-1', '6-2', '3-1', '1-1', '2-1', '5-1', '8-1', '5-2', '8-2', '7-1', '1-2', '4-2', '2-2', '7-2' ];
        $levels = ['4-1', '1-2'];
        $log->write("Fixing Pipes\n");
        foreach ($game->worlds as $world) {
            foreach ($world->levels as $level) {
                if (in_array($level->name, $levels)) {
                    if ($level->pipe_pointers) {
                        foreach ($level->pipe_pointers as list($entry, $exit)) {
                            $log->write("Fixing pipe in " . $level->name . " - new world is " . $world->num . "\n");
                            $new_world = $world->num - 1;

                            // entry
                            $entry_data = $this->rom->read($entry, 1);
                            $new_entry_data = (($new_world << 5) | ($entry_data & 0b00011111));
                            $this->rom->write($entry, pack('C*', $new_entry_data));
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
         */
        for($i = 0; $i < 8; $i++) {
            $this->rom->write(0x09fbb + $i, pack('C*', $this->trans->asciitosmb($text[$i])));
        }
    }

    public function makeFlags() {
        $this->flags[0] = $this->options['Pipe Transitions'][0];
        $this->flags[1] = $this->options['Shuffle Levels'][0];
        $this->flags[2] = $this->options['Castles Last'][1];
        $this->flags[3] = $this->options['Shuffle Enemies'][2];
        $s = implode("", $this->flags);
        print("Flags: $s\n");
        $this->makeSeedHash();
    }

    public function getFlags() {
        return implode("", $this->flags);
    }

    public function makeSeedHash() {
        // TODO: add md5 of input ROM to hashstring?!
        global $smbr_version;
        $hashstring = implode("", $this->flags) . strval($this->getSeed() . $smbr_version);
        $this->seedhash = hash("crc32b", $hashstring);
        //print("makeSeedHash()\n
        //          md5: " . hash("md5", $hashstring) . "\n
        //          crc: " . $this->seedhash . "\n
        //       md5crc: " . hash("crc32b", hash("md5", $hashstring)) . "\n");
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
        $game = new Game();
        print("\nOK - making randomized SMB ROM with seed $this->rng_seed\n");

        //  Shuffle Levels
        if($this->options['Shuffle Levels'] == "true") {
            if($this->options['Castles Last'] == "true") {
                $this->shuffleLevelsWithCastlesLast($game);
            } else {
                $this->shuffleAllLevels($game);
            }
        }

        //  Shuffle Enemies
        if($this->options['Shuffle Enemies'] == "true") {
            $this->shuffleEnemies();
        }

        // Fix Pipes
        $this->fixPipes($game);

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


