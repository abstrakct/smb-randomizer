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

use SMBR\Game;
use SMBR\ItemPools;
use SMBR\Level;
use SMBR\Translator;

// TODO: move these somewhere better!
const HammerTimeOffset = 0x512b;
const HammerTimeOffset2 = 0x5161;
const FireTimeOffset = 0x515d;
const BowserHPOffset = 0x457c;
const StartingLivesOffset = 0x107a;

function enemyIsInPool($o, $pool)
{
    foreach ($pool as $p) {
        if ($o == $p) {
            return true;
        }
    }

    return false;
}

class Randomizer
{
    public $flags;
    public $seedhash;
    protected $rng_seed;
    protected $seed;
    protected $options;
    protected $rom;
    private $level = [];
    private $trans;
    private $log;
    // TODO: move all enemy data to Enemy class
    public $enemy_pools;
    const VERSION = "0.8.2";

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
    public function __construct($seed = 1, $opt = null, $rom = null)
    {
        $this->rng_seed = $seed;
        $this->options = $opt;
        $this->rom = $rom;
        $this->trans = new Translator();
        $this->enemy_pools = new \SMBR\EnemyPools();
    }

    public function setLogger($log)
    {
        $this->log = $log;
    }

    public function printOptions()
    {
        print("\n\n*** OPTIONS ***\nSeed: $this->rng_seed\n");

        foreach ($this->options as $key => $value) {
            print("$key: $value\n");
        }
    }

    public function getSeed()
    {
        return $this->rng_seed;
    }

    public function setMarioColorScheme(string $colorscheme, Game &$game): self
    {
        global $colorschemes;
        $this->log->write("Mario Color Scheme: " . $colorscheme . "\n");
        if ($colorscheme == "random") {
            $this->setSeed();
            $outer = mt_rand(0, 255);
            $skin = mt_rand(0, 255);
            $inner = mt_rand(0, 255);
        } else {
            $outer = $colorschemes[$colorscheme]->outer;
            $skin = $colorschemes[$colorscheme]->skin;
            $inner = $colorschemes[$colorscheme]->inner;
        }
        $this->rom->setMarioOuterColor($outer, $game);
        $this->rom->setMarioSkinColor($skin, $game);
        $this->rom->setMarioInnerColor($inner, $game);
        return $this;
    }

    public function setFireColorScheme(string $colorscheme, Game &$game): self
    {
        global $colorschemes;
        $this->log->write("Fire Mario/Luigi Color Scheme: " . $colorscheme . "\n");
        if ($colorscheme == "random") {
            $this->setSeed();
            $outer = mt_rand(0, 255);
            $skin = mt_rand(0, 255);
            $inner = mt_rand(0, 255);
        } else {
            $outer = $colorschemes[$colorscheme]->outer;
            $skin = $colorschemes[$colorscheme]->skin;
            $inner = $colorschemes[$colorscheme]->inner;
        }
        $this->rom->setFireOuterColor($outer, $game);
        $this->rom->setFireSkinColor($skin, $game);
        $this->rom->setFireInnerColor($inner, $game);
        return $this;
    }

    public function setLuigiColorScheme(string $colorscheme, Game &$game): self
    {
        global $colorschemes;
        $this->log->write("Luigi Color Scheme: " . $colorscheme . "\n");
        if ($colorscheme == "random") {
            $this->setSeed();
            $outer = mt_rand(0, 255);
            $skin = mt_rand(0, 255);
            $inner = mt_rand(0, 255);
        } else {
            $outer = $colorschemes[$colorscheme]->outer;
            $skin = $colorschemes[$colorscheme]->skin;
            $inner = $colorschemes[$colorscheme]->inner;
        }
        $this->rom->setLuigiOuterColor($outer, $game);
        $this->rom->setLuigiSkinColor($skin, $game);
        $this->rom->setLuigiInnerColor($inner, $game);
        return $this;
    }

    public function randomizeEnemies(&$game, $in_pools = false)
    {
        $this->log->write("Randomizing enemies" . ($in_pools ? " in pools." : ".") . "\n");
        $vanilla = Level::all();
        foreach ($vanilla as $level) {
            if ($level->enemy_data_offset > 0x0000) {
                $m = "Randomizing enemies on level " . $level->name . "\n";
                $this->log->write($m);
                $this->randomizeEnemiesOnLevel($level->enemy_data_offset, $game, $in_pools);
            }
        }
    }

    public function randomizeEnemiesOnLevel($offset, &$game, $in_pools = false)
    {
        $end = 0;
        $percentage = 100; // if == 100 then all enemies will be randomized, if 50 there's a 50% chance of randomization happening for each enemy, etc.
        // TODO: change percentage based on settings/flags/something.
        // TODO: or remove this percentage setting?
        // TODO: improve enemy shuffling in general!? the code is a bit messy. but hey, it works!

        $data = $this->rom->read($offset, 100);
        foreach ($data as $byte) {
            $end++;
            if ($byte == 0xFF) {
                break;
            }
        }
        for ($i = 0; $i < $end; $i += 2) {
            $do_randomize = true;
            $x = $data[$i] & 0xf0;
            $y = $data[$i] & 0x0f;
            if ($y == 0xE) {
                $i++;
            } else if ($y > 0xE) {
                continue;
            } else {
                if ($data[$i] != 0xFF) {
                    $p = $data[$i + 1] & 0b10000000;
                    $h = $data[$i + 1] & 0b01000000;
                    $o = $data[$i + 1] & 0b00111111; // this is the enemy object

                    /* Some enemies can't be randomized, so let's check for those */
                    foreach ($this->enemy_pools->dont_randomize as $nope) {
                        if ($o == $nope) {
                            $do_randomize = false;
                        }
                    }

                    if ($do_randomize) {
                        if ($in_pools) {
                            $new_data = 0;
                            if (mt_rand(1, 100) <= $percentage) {
                                if ($o == Enemy::get('Toad')) {
                                    $z = count($this->enemy_pools->toad_pool);
                                    $new_object = $this->enemy_pools->toad_pool[mt_rand(0, count($this->enemy_pools->toad_pool) - 1)];
                                    $new_coord = 0xb8;
                                    $game->addData($offset + $i, pack('C*', $new_coord));
                                } else if (enemyIsInPool($o, $this->enemy_pools->generator_pool)) {
                                    $new_object = $this->enemy_pools->generator_pool[mt_rand(0, count($this->enemy_pools->generator_pool) - 1)];
                                } else if (enemyIsInPool($o, $this->enemy_pools->goomba_pool)) {
                                    $new_object = $this->enemy_pools->goomba_pool[mt_rand(0, count($this->enemy_pools->goomba_pool) - 1)];
                                } else if (enemyIsInPool($o, $this->enemy_pools->koopa_pool)) {
                                    $new_object = $this->enemy_pools->koopa_pool[mt_rand(0, count($this->enemy_pools->koopa_pool) - 1)];
                                } else if (enemyIsInPool($o, $this->enemy_pools->firebar_pool)) {
                                    $new_object = $this->enemy_pools->firebar_pool[mt_rand(0, count($this->enemy_pools->firebar_pool) - 1)];
                                } else if ($o == Enemy::get("Lakitu")) {
                                    $new_object = $this->enemy_pools->lakitu_pool[mt_rand(0, count($this->enemy_pools->lakitu_pool) - 1)];
                                }

                                $new_data = (($p | $h) | $new_object);
                                $game->addData($offset + $i + 1, pack('C*', $new_data));
                            }
                        } else {
                            $new_data = 0;
                            if (mt_rand(1, 100) <= $percentage) {
                                if ($o == Enemy::get('Toad')) {
                                    $new_object = $this->enemy_pools->toad_pool[mt_rand(0, count($this->enemy_pools->toad_pool) - 1)];
                                    $new_coord = 0xb8;
                                    $game->addData($offset + $i, pack('C*', $new_coord));
                                } else if ($o == Enemy::get('Bowser Fire Generator') or $o == Enemy::get('Red Flying Cheep-Cheep Generator') or $o == Enemy::get('Bullet Bill/Cheep-Cheep Generator')) {
                                    // TODO: should Bowser Fire Generator be included in this?
                                    $new_object = $this->enemy_pools->generator_pool[mt_rand(0, count($this->enemy_pools->generator_pool) - 1)];
                                } else {
                                    $new_object = $this->enemy_pools->reasonable_enemy_pool[mt_rand(0, count($this->enemy_pools->reasonable_enemy_pool) - 1)];
                                }

                                $new_data = (($p | $h) | $new_object);
                                $game->addData($offset + $i + 1, pack('C*', $new_data));
                            }
                        }
                    }
                }
            }
        }
    }

    public function randomizeBlocks(Game &$game, $frompool, $topool)
    {
        $this->log->write("Randomizing blocks!\n");

        // 0xFD signifies end of level object data!
        foreach ($game->worlds as $world) {
            foreach ($world->levels as $level) {
                if ($level->level_data_offset == 0x0000) {
                    break;
                }

                $end = 0;
                $data = $this->rom->read($level->level_data_offset, 200);
                foreach ($data as $byte) {
                    $end++;
                    if ($byte == 0xFD) {
                        break;
                    }
                }

                for ($i = 2; $i < $end; $i += 2) {
                    $do_randomize = true;
                    $y = $data[$i] & 0b00001111;
                    if ($y > 0x0B) {
                        $do_randomize = false;
                    }

                    if ($do_randomize) {
                        $p = $data[$i + 1] & 0b10000000;
                        $object = $data[$i + 1] & 0b01111111;
                        $new_data = 0x99;
                        if (in_array($object, $frompool)) {
                            $pull_key = mt_rand(0, count($topool) - 1);
                            $new_data = $topool[$pull_key];
                            $new_object = $p | $new_data;
                            $game->addData($level->level_data_offset + $i + 1, pack('C*', $new_object));
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
    public function shuffleAllLevels(&$game)
    {
        $all_levels = ['1-1', '1-2', '1-3', '1-4', '2-1', '2-2', '2-3', '2-4', '3-1', '3-2', '3-3', '3-4', '4-1', '4-2', '4-3', '4-4', '5-1', '5-2', '5-3', '5-4', '6-1', '6-2', '6-3', '6-4', '7-1', '7-2', '7-3', '7-4', '8-1', '8-2', '8-3'];

        $this->log->write("Shuffling ALL levels\n");
        $shuffledlevels = mt_shuffle($all_levels);
        //print_r($shuffledlevels);

        $lastlevelindex = 0;
        $levelindex = 1;
        $worldindex = 1;
        $shuffleindex = 0;

        // TODO: reduce code duplication!
        if ($this->options['pipe-transitions'] == 'remove') {
            for ($i = 0; $i < count($shuffledlevels); $i++) {
                $game->worlds[$worldindex]->levels[$levelindex] = Level::get($shuffledlevels[$shuffleindex]);
                if (Level::get($shuffledlevels[$shuffleindex])->map >= 0x60 and Level::get($shuffledlevels[$shuffleindex])->map <= 0x65) {
                    // it's a castle, so increase the world index and reset the level index
                    $worldindex++;
                    if ($worldindex > 8) {
                        $worldindex = 8;
                    }

                    $levelindex = 0;
                }
                $lastlevelindex = $levelindex;
                $levelindex++;
                $shuffleindex++;
            }
            $game->worlds[8]->levels[$lastlevelindex + 1] = Level::get('8-4');
        } else if ($this->options['pipe-transitions'] == 'keep') {
            for ($i = 0; $i < count($shuffledlevels); $i++) {
                $game->worlds[$worldindex]->levels[$levelindex] = Level::get($shuffledlevels[$shuffleindex]);
                $levelindex++;

                if (Level::get($shuffledlevels[$shuffleindex])->map >= 0x60 and Level::get($shuffledlevels[$shuffleindex])->map <= 0x65) {
                    // it's a castle, so increase the world index and reset the level index
                    $worldindex++;
                    if ($worldindex > 8) {
                        $worldindex = 8;
                    }

                    $levelindex = 0;
                }

                if ($shuffleindex < 30) {
                    if (in_array(Level::get($shuffledlevels[$shuffleindex + 1])->map, [Level::get('1-2')->map, Level::get('2-2')->map, Level::get('4-2')->map])) {
                        $game->worlds[$worldindex]->levels[$levelindex] = Level::get('Pipe Transition');
                    }
                }

                $levelindex++;
                $shuffleindex++;
            }
            $lastlevelindex = count($game->worlds[8]->levels) + 2;
            $game->worlds[8]->levels[$lastlevelindex] = Level::get('8-4');
        }
    }

    /*
     * Shuffle World Order - produce a seed where the worlds and their levels stay vanilla,
     * but the order the worlds appear in is shuffled.
     * World 8 will always be last.
     */
    public function shuffleWorldOrder(&$game)
    {
        $worlds = [1, 2, 3, 4, 5, 6, 7];
        $shuffledworlds = mt_shuffle($worlds);

        // shuffle worlds 1-7
        for ($i = 1; $i <= 7; $i++) {
            switch ($shuffledworlds[$i - 1]) {
                case 1:
                    if ($this->options['pipe-transitions'] == 'keep') {
                        $game->worlds[$i] = new World1($game, $i);
                    }

                    if ($this->options['pipe-transitions'] == 'remove') {
                        $game->worlds[$i] = new World1NoPipeTransition($game, $i);
                    }

                    break;
                case 2:
                    if ($this->options['pipe-transitions'] == 'keep') {
                        $game->worlds[$i] = new World2($game, $i);
                    }

                    if ($this->options['pipe-transitions'] == 'remove') {
                        $game->worlds[$i] = new World2NoPipeTransition($game, $i);
                    }

                    break;
                case 3:
                    $game->worlds[$i] = new World3($game, $i);
                    break;
                case 4:
                    if ($this->options['pipe-transitions'] == 'keep') {
                        $game->worlds[$i] = new World4($game, $i);
                    }

                    if ($this->options['pipe-transitions'] == 'remove') {
                        $game->worlds[$i] = new World4NoPipeTransition($game, $i);
                    }

                    break;
                case 5:
                    $game->worlds[$i] = new World5($game, $i);
                    break;
                case 6:
                    $game->worlds[$i] = new World6($game, $i);
                    break;
                case 7:
                    $game->worlds[$i] = new World7($game, $i);
                    break;
            }
        }

        // set world 8 as world 8
        $game->worlds[8] = new World8($game, 8);
        // set the shuffled worlds to their vanilla layout
        $game->setVanilla();
        //print_r($game);
    }

    /*
     * Shuffle levels, but make sure each -4 is a castle.
     * Castles are also shuffled, except the 8-4 which is 8-4
     * TODO: add keeping pipe transitions in place.
     */
    public function shuffleLevelsWithNormalWorldLength(&$game)
    {
        $levels = ['1-1', '1-2', '1-3', '2-1', '2-2', '2-3', '3-1', '3-2', '3-3', '4-1', '4-2', '4-3', '5-1', '5-2', '5-3', '6-1', '6-2', '6-3', '7-1', '7-2', '7-3', '8-1', '8-2', '8-3'];
        $castles = ['1-4', '2-4', '3-4', '4-4', '5-4', '6-4', '7-4'];

        $this->log->write("Shuffling levels (normal world length)\n");

        $shuffledlevels = mt_shuffle($levels);
        $shuffledcastles = mt_shuffle($castles);

        if ($this->options['pipe-transitions'] == 'remove') {
            $this->log->write("Removing pipe transitions\n");
            $levelindex = 0;
            $castleindex = 0;
            for ($w = 1; $w <= 8; $w++) {
                for ($i = 0; $i < 3; $i++) {
                    $game->worlds[$w]->levels[$i] = Level::get($shuffledlevels[$levelindex]);
                    $levelindex++;
                }
                if ($castleindex < 7) {
                    $game->worlds[$w]->levels[3] = Level::get($shuffledcastles[$castleindex]);
                }

                $castleindex++;
            }
            $game->worlds[8]->levels[3] = Level::get('8-4');
        } else if ($this->options['pipe-transitions'] == 'keep') {
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

            //$this->log->write("Keeping pipe transitions\n");
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

    public function randomizeBowserAbilities(&$game)
    {
        $this->log->write("Randomizing Bowser's abilities.\n");

        $new_hammer_world = mt_rand(0, 6);
        $new_fire_world = mt_rand($new_hammer_world, 7);
        //print("Hammer: $new_hammer_world\nFire: $new_fire_world\n");

        $game->addData(HammerTimeOffset, pack('C*', $new_hammer_world));
        $game->addData(HammerTimeOffset2, pack('C*', $new_hammer_world));
        $game->addData(FireTimeOffset, pack('C*', $new_fire_world));
    }

    public function randomizeBowserHitpoints(&$game)
    {
        $this->log->write("Randomizing Bowser's hitpoints.\n");

        if ($this->options['bowser-hitpoints'] == "easy") {
            $new_hitpoints = mt_rand(1, 5);
        } else if ($this->options['bowser-hitpoints'] == "medium") {
            $new_hitpoints = mt_rand(5, 10);
        } else if ($this->options['bowser-hitpoints'] == "hard") {
            $new_hitpoints = mt_rand(10, 20);
        } else if ($this->options['bowser-hitpoints'] == "random") {
            $new_hitpoints = mt_rand(1, 20);
        } else {
            echo "Invalid value for option Bowser Hitpoints!";
            exit(1);
        }

        $this->log->write("Bowser's hitpoints: " . $new_hitpoints . "\n");
        $game->addData(BowserHPOffset, pack('C*', $new_hitpoints));
    }

    public function randomizeStartingLives(&$game)
    {
        $this->log->write("Randomizing player's starting lives.\n");

        if ($this->options['starting-lives'] == "easy") {
            $new_lives = mt_rand(7, 10);
        } else if ($this->options['starting-lives'] == "medium") {
            $new_lives = mt_rand(4, 6);
        } else if ($this->options['starting-lives'] == "hard") {
            $new_lives = mt_rand(1, 3);
        } else if ($this->options['starting-lives'] == "very-hard") {
            $new_lives = 1;
        } else if ($this->options['starting-lives'] == "random") {
            $new_lives = mt_rand(1, 19);
        } else {
            echo "Invalid value for option Starting Lives!";
            exit(1);
        }

        $this->log->write("Player starting lives: " . $new_lives . "\n");
        $game->addData(StartingLivesOffset, pack('C*', $new_lives - 1));
    }

    public function randomizeWarpZones(&$game)
    {
        $offset = 0x802;

        if ($this->options['warp-zones'] == 'random') {
            $this->log->write("Randomizing Warp Zones.\n");
            for ($i = 0; $i < 11; $i++) {
                $new_warp = mt_rand(1, 8);
                $game->addData($offset + $i, pack('C*', $new_warp));
            }
            // cheap and easy fix
            $new_warp = 0x00;
            $game->addData(0x805, pack('C*', $new_warp));
            $game->addData(0x809, pack('C*', $new_warp));
        } else if ($this->options['warp-zones'] == 'shuffle') {
            $this->log->write("Shuffling Warp Zones.\n");
            $destinations = [1, 2, 3, 4, 5, 6, 7, 8, 0x24]; // 0x24 is "blank"
            $shuffled_destinations = mt_shuffle($destinations);
            $index = 0;

            for ($i = 0; $i <= 9; $i += 4) {
                $game->addData($offset + $i, pack('C*', $shuffled_destinations[$index]));
                $game->addData($offset + $i + 1, pack('C*', $shuffled_destinations[$index + 1]));
                $game->addData($offset + $i + 2, pack('C*', $shuffled_destinations[$index + 2]));
                $index += 3;
            }
        } else {
            echo "Invalid value for option Warp Zones!";
            exit(1);
        }
    }

    public function fixPipes(Game &$game)
    {
        $levels = ['4-1', '1-2', '2-1', '1-1', '3-1', '4-1', '4-2', '5-1', '5-2', '6-2', '7-1', '8-1', '8-2', '2-2', '7-2'];
        $this->log->write("Fixing Pipes\n");
        foreach ($game->worlds as $world) {
            foreach ($world->levels as $level) {
                if (in_array($level->name, $levels)) {
                    if ($level->pipe_pointers) {
                        foreach ($level->pipe_pointers as list($entry, $exit)) {
                            $this->log->write("Fixing pipe in " . $level->name . " - new world is " . $world->num . "\n");
                            $new_world = $world->num - 1;

                            // entry
                            if ($entry != null) {
                                $entry_data = $this->rom->read($entry, 1);
                                $new_entry_data = (($new_world << 5) | ($entry_data & 0b00011111));
                                $game->addData($entry, pack('C*', $new_entry_data));
                            }
                            // exit
                            if ($exit != null) {
                                $exit_data = $this->rom->read($exit, 1);
                                $new_exit_data = (($new_world << 5) | ($exit_data & 0b00011111));
                                $game->addData($exit, pack('C*', $new_exit_data));
                            }
                        }
                    }
                }
            }
        }
    }

    public function fixMidwayPoints(Game &$game)
    {
        $this->log->write("Fixing midway points:\n");

        if (($this->options['shuffle-levels'] == 'all' && $this->options['normal-world-length'] == 'false') ||
            ($this->options['shuffle-levels'] == 'worlds')) {
            // Remove midway points
            $this->log->write("Removing all midway points!\n");
            for ($i = 0; $i < 0xF; $i++) {
                $game->midway_points[$i] = 0x00;
            }
        }

        if ($this->options['shuffle-levels'] == 'all' && $this->options['normal-world-length'] == 'true') {
            // Fix midway points
            $this->log->write("Moving midway points around to correct positions!\n");
            $mpindex = 0;
            foreach ($game->worlds as $world) {
                $game->midway_points[$mpindex] = ($world->levels[0]->midway_point << 4) | ($world->levels[1]->midway_point);
                $mpindex++;
                $game->midway_points[$mpindex] = ($world->levels[2]->midway_point << 4) | ($world->levels[3]->midway_point);
                $mpindex++;
            }
        }
    }

    public function setTextSeedhash(string $text, Game &$game)
    {
        $offset = 0x9fa5;
        $this->log->write("Writing Seedhash on title screen\n");

        $game->addData($offset, pack('C*', $this->trans->asciitosmb('H')));
        $game->addData($offset + 1, pack('C*', $this->trans->asciitosmb('A')));
        $game->addData($offset + 2, pack('C*', $this->trans->asciitosmb('S')));
        $game->addData($offset + 3, pack('C*', $this->trans->asciitosmb('H')));
        $game->addData($offset + 4, pack('C*', $this->trans->asciitosmb(' ')));
        /*
         * Write the first 8 characters of the seedhash on title screen.
         * TODO: see if there's a way to draw some sprites instead!
         * DONE: there probably is, but no good sprites it seems. Text/numbers is better.
         */
        for ($i = 0; $i < 8; $i++) {
            $game->addData($offset + 5 + $i, pack('C*', $this->trans->asciitosmb($text[$i])));
        }
    }

    public function setText(&$game, $text, $newtext)
    {
        $messages = [
            // first 3 bytes = header, last = 0x00 - keep those!
            "ThankYouMario" => [0xd64, 0xd77], // THANK YOU MARIO!
            "ThankYouLuigi" => [0xd78, 0xd8b], // THANK YOU LUIGI!
            "AnotherCastle" => [0xd8c, 0xdb7], // BUT OUR PRINCESS IS IN \ ANOTHER CASTLE!
            "QuestOver" => [0xdb8, 0xdce], // YOUR QUEST IS OVER.
            "NewQuest" => [0xdcf, 0xded], // WE PRESENT YOU A NEW QUEST.
            "WorldSelect" => [0xdee, 0xdfe], // PUSH BUTTON B
            "WorldSelect2" => [0xdff, 0xe13], // TO SELECT A WORLD
        ];

        $offset = $messages[$text][0] + 3;
        $lastoffset = $messages[$text][1];
        for ($i = 0; $i < strlen($newtext); $i++) {
            if ($i + $offset > $lastoffset) {
                print("ERROR: Text exceeds max length! Text is $text\n");
                exit(1);
            }

            if ($newtext[$i] == '/') {
                $offset += 2;
                $i++;
            }

            $game->addData($offset + $i, pack('C*', $this->trans->asciitosmb($newtext[$i])));
        }
    }

    public function shuffleText(&$game, $text, $variations)
    {
        // Set new seed for rng to separate this randomization from game randomization
        $this->setSeed();
        $variation = mt_rand(0, count($variations) - 1);
        $this->log->write("Shuffling text " . $text . " to variation " . $variation . " (" . $variations[$variation] . ")\n");

        $this->setText($game, $text, $variations[$variation]);
    }

    // Do this one separately
    public function shuffleWinText(&$game, $win_variations)
    {
        // Set new seed for rng to separate this randomization from game randomization
        $this->setSeed();
        $variation = mt_rand(0, count($win_variations) - 1);

        $this->setText($game, "QuestOver", $win_variations[$variation][0]);
        $this->setText($game, "NewQuest", $win_variations[$variation][1]);
        $this->setText($game, "WorldSelect", $win_variations[$variation][2]);
        $this->setText($game, "WorldSelect2", $win_variations[$variation][3]);
        $this->log->write("Randomized win text to entry " . $variation . " (" . $win_variations[$variation][0] . ")\n");
    }

    public function getFlags()
    {
        return implode("", $this->flags);
    }

    public function makeFlags()
    {
        $this->flags[0] = $this->options['pipe-transitions'][2];
        $this->flags[1] = $this->options['shuffle-levels'][1];
        $this->flags[1]++;
        $this->flags[1]++;
        $this->flags[2] = $this->options['normal-world-length'][1];
        $this->flags[2]++;
        $this->flags[2]++;
        $this->flags[3] = $this->options['enemies'][12];
        $this->flags[4] = $this->options['blocks'][10];
        $this->flags[5] = $this->options['bowser-abilities'][3];
        $this->flags[6] = $this->options['bowser-hitpoints'][0];
        $this->flags[6]++;
        $this->flags[7] = $this->options['starting-lives'][0];
        $this->flags[7]++;
        $this->flags[7]++;
        $this->flags[8] = $this->options['warp-zones'][2];
        $this->flags[8]++;
        $this->flags[8]++;
        $this->flags[8]++;

        $s = implode("", $this->flags);
        $f = strtoupper($s);
        print("Flags: $f\n");

        $this->makeSeedHash();
    }

    public function makeSeedHash()
    {
        // TODO - thought/idea:
        // include the MD5 of the randomized ROM in the seedhash?
        // - it would have to be the MD5 of the file BEFORE writing the seedhash though.
        // - but is it necessary? probably not. it could be an extra guarantee that the
        // files are identical, but there shouldn't be any risk of collisions, right?
        $hashstring = implode("", $this->flags) . strval($this->getSeed() . \SMBR\Randomizer::VERSION . $this->rom->getMD5());
        $this->seedhash = hash("crc32b", $hashstring);

        print("SeedHash: $this->seedhash\n");
    }

    public function getSeedHash()
    {
        return $this->seedhash;
    }

    public function setSeed(int $rng_seed = null)
    {
        $rng_seed = $rng_seed ?: random_int(1, 9999999999); // cryptographic pRNG for seeding
        $this->rng_seed = $rng_seed % 10000000000;
        mt_srand($this->rng_seed);
    }

    // Here we go!
    public function makeSeed()
    {
        include "TextVariations.php";

        $game = new Game($this->options);
        $item_pools = new ItemPools();

        $game->worlds = [
            '1' => new World1($game, 1),
            '2' => new World2($game, 2),
            '3' => new World3($game, 3),
            '4' => new World4($game, 4),
            '5' => new World5($game, 5),
            '6' => new World6($game, 6),
            '7' => new World7($game, 7),
            '8' => new World8($game, 8),
        ];

        print("\nHere we go! Making randomized SMB ROM with seed $this->rng_seed\n");

        //  Shuffle Levels
        if ($this->options['shuffle-levels'] == "all") {
            if ($this->options['normal-world-length'] == "true") {
                $this->shuffleLevelsWithNormalWorldLength($game);
            } else {
                $this->shuffleAllLevels($game);
            }
        } else if ($this->options['shuffle-levels'] == "worlds") {
            $game->setVanilla();
            $this->shuffleWorldOrder($game);
        } else if ($this->options['shuffle-levels'] == "none") {
            $game->setVanilla();
        } else {
            print("Unrecognized option " . $this->options['shuffle-levels'] . " for Shuffle Levels! Exiting...");
            exit(1);
        }

        //  Shuffle Enemies
        if ($this->options['enemies'] == "randomize-full") {
            $this->randomizeEnemies($game, false);
        } else if ($this->options['enemies'] == "randomize-pools") {
            $this->randomizeEnemies($game, true);
        }

        // Shuffle Blocks
        if ($this->options['blocks'] == "randomize-all") {
            $this->randomizeBlocks($game, $item_pools->all_items, $item_pools->all_items);
        } else if ($this->options['blocks'] == "randomize-powerups") {
            $this->randomizeBlocks($game, $item_pools->powerups, $item_pools->powerups);
        } else if ($this->options['blocks'] == "randomize-grouped") {
            $this->randomizeBlocks($game, $item_pools->all_question_blocks, $item_pools->all_question_blocks);
            $this->randomizeBlocks($game, $item_pools->all_hidden_blocks, $item_pools->all_hidden_blocks);
            $this->randomizeBlocks($game, $item_pools->all_brick_blocks, $item_pools->all_brick_blocks);
        } else if ($this->options['blocks'] == "randomize-coins") {
            $this->randomizeBlocks($game, $item_pools->all_items, $item_pools->all_coins);
        } else if ($this->options['blocks'] == "randomize-none") {
            $this->log->write("No randomization of blocks!\n");
        }

        // Randomize Bowser's Abilities
        if ($this->options['bowser-abilities'] == "true") {
            $this->randomizeBowserAbilities($game);
        }

        // Randomize Bowser's Hitpoints
        if ($this->options['bowser-hitpoints'] != "normal") {
            $this->randomizeBowserHitpoints($game);
        }

        // Randomize player's starting lives
        if ($this->options['starting-lives'] != "normal") {
            $this->randomizeStartingLives($game);
        }

        // Randomize warp zones
        if ($this->options['warp-zones'] != "normal") {
            $this->randomizeWarpZones($game);
        }

        // Fix Pipes
        $this->fixPipes($game);

        // Fix Midway Points
        $this->fixMidwayPoints($game);

        // Set seedhash text
        $this->setTextSeedhash($this->seedhash, $game);

        // Shuffle texts
        $this->shuffleText($game, "ThankYouMario", $thank_you_mario_variations);
        $this->shuffleText($game, "ThankYouLuigi", $thank_you_luigi_variations);
        $this->shuffleText($game, "AnotherCastle", $another_castle_variations);
        $this->shuffleWinText($game, $win_variations);

        // Set colorschemes
        $this->setMarioColorScheme($this->options['mariocolors'], $game);
        $this->setLuigiColorScheme($this->options['luigicolors'], $game);
        $this->setFireColorScheme($this->options['firecolors'], $game);

        return $game;
    }
}

// Shuffle the contents of an array.
// Taken from ALttP Randomizer
function mt_shuffle(array $array)
{
    $new_array = [];
    while (count($array)) {
        $pull_key = mt_rand(0, count($array) - 1);
        $new_array = array_merge($new_array, array_splice($array, $pull_key, 1));
    }

    return $new_array;
}
