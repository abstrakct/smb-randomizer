<?php namespace SMBR;

use SMBR\Colorscheme;
use SMBR\Translator;

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
    public $flags;
    public $seedhash;
    public $colorschemes;
    protected $rng_seed;
    protected $seed;
    protected $options;
    protected $rom;
    private $level = [];
    private $log;
    private $fullenemypool = [ 0x00, 0x01, 0x02, 0x03, 0x04, 0x05, 0x06, 0x07, 0x08, 0x09, 0x0A, 0x0B, 0x0C, 0x0D, 0x0E, 0x0F, 0x10, 0x11, 0x12, 0x14, 0x15, 0x16, 0x17, 0x1B, 0x1C, 0x1D, 0x1E, 0x1F, 0x2D, 0x37, 0x38, 0x39, 0x3A, 0x3B, 0x3C, 0x3D, 0x3E ];
    private $testenemypool = [ 0x00, 0x01, 0x02, 0x03, 0x04, 0x05, 0x06 ];
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
        $this->log->write("Mario Color Scheme: " . $colorscheme . "\n");
        if($colorscheme == "random") {
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
        $this->log->write("Fire Mario/Luigi Color Scheme: " . $colorscheme . "\n");
        if($colorscheme == "random") {
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
        $this->log->write("Luigi Color Scheme: " . $colorscheme . "\n");
        if($colorscheme == "random") {
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
        $offset = $_SESSION['enemydataoffsets'][$level];
        $end = 0;
        $data = $this->rom->read($offset, 100);
        foreach ($data as $byte) {
            $end++;
            if($byte == 0xFF) {
                break;
            }
        }
        for($i = 0; $i < $end; $i+=2) {
            $x = $data[$i] & 0xf0;
            $y = $data[$i] & 0x0f;
            if($y == 0xE) {
                $i++;
            } else if ($y > 0x0e) {
                continue;
            } else {
                if($data[$i] != 0xFF) {
                    $p = $data[$i+1] & 0x80;
                    $h = $data[$i+1] & 0x40;
                    $o = $data[$i+1] & 0x3f;  // this is the enemy
                    $newo = $this->testenemypool[mt_rand(0, count($this->testenemypool) - 1)];
                    $newdata = (($data[$i+1] & 0x80) | ($data[$i+1] & 0x40)) | $newo;
                    $data[$i+1] = $newdata;
                    $this->rom->write($offset + $i + 1, pack('C*', $newdata));
                    //printf("x: %02x  y: %02x  p: %02x  h: %02x  o: %02x (%s)\n", $x, $y, $p, $h, $o, getenemyname($newo));
                }
            }
        }
    }

    public function shuffleEnemies() {
        foreach ($_SESSION['enemydataoffsets'] as $key => $value) {
            $this->shuffleEnemiesOnLevel($key);
        }
        //$this->shuffleEnemiesOnLevel('1-1');
        //$this->shuffleEnemiesOnLevel('1-2');
        //$this->shuffleEnemiesOnLevel('1-3');
        //$this->shuffleEnemiesOnLevel('1-4');
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

    public function setTextRando() {
        $this->rom->write(0x09fb5, pack('C*', $this->trans->asciitosmb('R')));
        $this->rom->write(0x09fb6, pack('C*', $this->trans->asciitosmb('A')));
        $this->rom->write(0x09fb7, pack('C*', $this->trans->asciitosmb('N')));
        $this->rom->write(0x09fb8, pack('C*', $this->trans->asciitosmb('D')));
        $this->rom->write(0x09fb9, pack('C*', $this->trans->asciitosmb('O')));
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
        $this->flags[2] = $this->options['Castles Last'][0];
        $this->flags[3] = $this->options['Shuffle Enemies'][0];
        $s = implode("", $this->flags);
        print("Flags: $s\n");
        $this->makeSeedHash();
    }

    public function makeSeedHash() {
        $hashbase = implode("", $this->flags) . strval($this->getSeed());
        $this->seedhash = md5($hashbase);
        print("SeedHash: $this->seedhash\n");
    }

    public function setSeed(int $rng_seed = null) {
        $rng_seed = $rng_seed ?: random_int(1, 999999999); // cryptographic pRNG for seeding
		$this->rng_seed = $rng_seed % 1000000000;
		mt_srand($this->rng_seed);
    }

    // Here we go!
    public function makeSeed() {
        $this->makeFlags();
        $this->setTextRando();
        $this->setTextSeedhash($this->seedhash);
        print("\nOK - making randomized SMB ROM with seed $this->rng_seed\n");
        $this->log = $_SESSION['log'];

        $this->setMarioColorScheme($this->options['Mario Color Scheme']);
        $this->setLuigiColorScheme($this->options['Luigi Color Scheme']);
        $this->setFireColorScheme($this->options['Fire Color Scheme']);

        if($this->options['Shuffle Levels'] == "true") {
            if($this->options['Castles Last'] == "true") {
                $this->shuffleLevelsWithCastlesLast();
            } else {
                $this->shuffleAllLevels();
            }
        }
        if($this->options['Shuffle Enemies'] == "true") {
            $this->shuffleEnemies();
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


