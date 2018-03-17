<?php

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

//include "levels.php";
if(!session_id()) session_start();

class Randomizer {
    protected $rng_seed;
    protected $seed;
    protected $options;
    protected $rom;
    private $level = [];
    private $log;

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
        $this->log->write("Mario Color Scheme: " . $colorscheme . "\n");
        if($colorscheme == "normal") {
            return $this;
        }
        if($colorscheme == "random") {
            $outer = mt_rand(0, 255);
            $skin = mt_rand(0, 255);
            $inner = mt_rand(0, 255);
            $this->rom->setMarioInnerColor($inner);
            $this->rom->setMarioSkinColor($skin);
            $this->rom->setMarioOuterColor($outer);
        }
        return $this;
    }

    public function setFireColorScheme(string $colorscheme) : self {
        $this->log->write("Fire Mario/Luigi Color Scheme: " . $colorscheme . "\n");
        if($colorscheme == "normal") {
            return $this;
        }
        if($colorscheme == "random") {
            $outer = mt_rand(0, 255);
            $skin = mt_rand(0, 255);
            $inner = mt_rand(0, 255);
            $this->rom->setFireInnerColor($inner);
            $this->rom->setFireSkinColor($skin);
            $this->rom->setFireOuterColor($outer);
        }
        return $this;
    }

    public function setLuigiColorScheme(string $colorscheme) : self {
        $this->log->write("Luigi Color Scheme: " . $colorscheme . "\n");
        if($colorscheme == "normal") {
            return $this;
        }
        if($colorscheme == "random") {
            $outer = mt_rand(0, 255);
            $skin  = mt_rand(0, 255);
            $inner = mt_rand(0, 255);
            $this->rom->setLuigiInnerColor($inner);
            $this->rom->setLuigiSkinColor($skin);
            $this->rom->setLuigiOuterColor($outer);
        }
        return $this;
    }

    /*
     * Shuffle levels, but castles can appear anywhere, except the 8-4 which is 8-4
     */
    public function shuffleAllLevels() {
        $this->log->write("Shuffling ALL levels\n");
        $shuffledlevels  = mt_shuffle($_SESSION['all_levels']);

        $levelindex = 1;
        $castleindex = 0;
        if($this->options['Pipe Transitions'] == 'remove') {
            for($i = 0; $i < 32; $i++) {
                $this->level[$levelindex] = $shuffledlevels[$i];
                $levelindex++;
            }

            $this->level[31] = 0x65;

            for($i = 1; $i <= 32; $i++) {
                $this->rom->write(0x1ccb + $i, pack('C*', $this->level[$i]));
            }

            $p = 0;
            for($i = 0; $i < 8; $i++) {
                $this->rom->write(0x1cc4 + $i, pack('C*', $p));
                $p += 4;
            }
        } else {
            print("not implemented\n");
        }
    }
    /*
     * Shuffle levels, but make sure each -4 is a castle.
     * Castles are also shuffled, except the 8-4 which is 8-4
     */
    public function shuffleLevelsWithCastlesLast() {
        $this->log->write("Shuffling levels\n");
        $shuffledlevels  = mt_shuffle($_SESSION['levels']);
        $shuffledcastles = mt_shuffle($_SESSION['castles']);

        $levelindex = 1;
        $castleindex = 0;
        if($this->options['Pipe Transitions'] == 'remove') {
            for($i = 0; $i <= 23; $i++) {
                $this->level[$levelindex] = $shuffledlevels[$i];
                $levelindex++;
                if(($levelindex % 4) == 0) $levelindex++;
            }

            for($i = 4; $i <= 28; $i += 4) {
                $this->level[$i] = $shuffledcastles[$castleindex];
                $castleindex++;
            }

            $this->level[32] = 0x65;

            for($i = 1; $i <= 32; $i++) {
                $this->rom->write(0x1ccb + $i, pack('C*', $this->level[$i]));
            }

            $p = 0;
            for($i = 0; $i < 8; $i++) {
                $this->rom->write(0x1cc4 + $i, pack('C*', $p));
                $p += 4;
            }
        } else {
            /*
             * https://github.com/Coolcord/Level-Headed/blob/master/SMB1/Research_Docs/Room%20Order%20Table.csv
            for($i = 0; $i < 23; $i++) {
                $this->level[$levelindex] = $shuffledlevels[$i];
                if($shuffledlevels[$i] == 0x25 or $shuffledlevels[$i] == 0x28 or $shuffledlevels[$i] == 0x22 or $shuffledlevels[$i] = 0x33) {
                    // then next level is pipe transition
                    $levelindex++;
                    $this->level[$levelindex] = 0x29;
                }
                $levelindex++;
                if(($levelindex % 4) == 0) $levelindex++;
            }

            for($i = 4; $i < 28; $i += 4) {
                $this->level[$i] = $shuffledcastles[$castleindex];
                $castleindex++;
            }

            $this->level[31] = 0x65;

            for($i = 1; $i <= 32; $i++) {
                $this->rom->write(0x1ccb + $i, pack('C*', $this->level[$i]));
            }

            $p = 0;
            for($i = 0; $i < 8; $i++) {
                $this->rom->write(0x1cc4 + $i, pack('C*', $p));
                $p += 4;
            }
             */
            print("not implemented\n");
        }
    }

    public function setSeed(int $rng_seed = null) {
        $rng_seed = $rng_seed ?: random_int(1, 999999999); // cryptographic pRNG for seeding
		$this->rng_seed = $rng_seed % 1000000000;
		mt_srand($this->rng_seed);
    }

    // Here we go!
    public function makeSeed() {
        print("\nOK - making randomized SMB ROM with seed $this->rng_seed\n");
        $this->log = $_SESSION['log'];

        $this->setMarioColorScheme($this->options['Mario Color Scheme']);
        $this->setLuigiColorScheme($this->options['Luigi Color Scheme']);
        $this->setFireColorScheme($this->options['Fire Color Scheme']);

        if($this->options['Randomize Levels'] == "true") {
            if($this->options['Castles Last'] == "true") {
                $this->shuffleLevelsWithCastlesLast();
            } else {
                $this->shuffleAllLevels();
            }
        }
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


?>
