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

use SMBR\Colorscheme;
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
const WarpZonesOffset = 0x0802;
const WarpZone12Offset = 0x0802;
const WarpZone42SkyOffset = 0x080a;
const WarpZone42EndOffset = 0x0807;
const FireworksOffset1 = 0x5308;
const FireworksOffset2 = 0x530e;
const FireworksOffset3 = 0x5314;

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
    const VERSION = "0.8.6";

    // Color schemes. TODO: improve
    public $colorschemes = [];

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
        $this->colorschemes = array('random' => new Colorscheme(0, 0, 0),
            'Vanilla Mario' => new Colorscheme(0x16, 0x27, 0x18),
            'Vanilla Luigi' => new Colorscheme(0x30, 0x27, 0x19),
            'Vanilla Fire' => new Colorscheme(0x37, 0x27, 0x16),
            'Pale Ninja' => new Colorscheme(0xce, 0xd0, 0x1e),
            'All Black' => new Colorscheme(0x8d, 0x8d, 0x8d),
            'Black & Blue' => new Colorscheme(0xcc, 0x18, 0x2f),
            'Black & Blue 2' => new Colorscheme(0x51, 0xf8, 0x6e),
            'Denim' => new Colorscheme(0x80, 0xa7, 0xcc),
            'Mustard Man' => new Colorscheme(0xd8, 0x27, 0x28),
        );
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

    public function printOptionsToLog()
    {
        foreach ($this->options as $key => $value) {
            $this->log->write("$key: $value\n");
        }
    }

    public function getSeed()
    {
        return $this->rng_seed;
    }

    public function setMarioColorScheme(string $colorscheme, Game &$game)
    {
        $this->log->write("Mario Color Scheme: " . $colorscheme . "\n");
        if ($colorscheme == "random") {
            $this->setSeed();
            $outer = mt_rand(0, 255);
            $skin = mt_rand(0, 255);
            $inner = mt_rand(0, 255);
        } else {
            $outer = $this->colorschemes[$colorscheme]->outer;
            $skin = $this->colorschemes[$colorscheme]->skin;
            $inner = $this->colorschemes[$colorscheme]->inner;
        }
        $this->rom->setMarioOuterColor($outer, $game);
        $this->rom->setMarioSkinColor($skin, $game);
        $this->rom->setMarioInnerColor($inner, $game);
    }

    public function setFireColorScheme(string $colorscheme, Game &$game)
    {
        global $colorschemes;
        $this->log->write("Fire Mario/Luigi Color Scheme: " . $colorscheme . "\n");
        if ($colorscheme == "random") {
            $this->setSeed();
            $outer = mt_rand(0, 255);
            $skin = mt_rand(0, 255);
            $inner = mt_rand(0, 255);
        } else {
            $outer = $this->colorschemes[$colorscheme]->outer;
            $skin = $this->colorschemes[$colorscheme]->skin;
            $inner = $this->colorschemes[$colorscheme]->inner;
        }
        $this->rom->setFireOuterColor($outer, $game);
        $this->rom->setFireSkinColor($skin, $game);
        $this->rom->setFireInnerColor($inner, $game);
    }

    public function setLuigiColorScheme(string $colorscheme, Game &$game)
    {
        global $colorschemes;
        $this->log->write("Luigi Color Scheme: " . $colorscheme . "\n");
        if ($colorscheme == "random") {
            $this->setSeed();
            $outer = mt_rand(0, 255);
            $skin = mt_rand(0, 255);
            $inner = mt_rand(0, 255);
        } else {
            $outer = $this->colorschemes[$colorscheme]->outer;
            $skin = $this->colorschemes[$colorscheme]->skin;
            $inner = $this->colorschemes[$colorscheme]->inner;
        }
        $this->rom->setLuigiOuterColor($outer, $game);
        $this->rom->setLuigiSkinColor($skin, $game);
        $this->rom->setLuigiInnerColor($inner, $game);
    }

    public function randomizeEnemies(&$game, $in_pools = false)
    {
        $this->log->write("Randomizing enemies" . ($in_pools ? " in pools." : ".") . "\n");
        $vanilla = Level::all();
        foreach ($vanilla as $level) {
            if ($level->has_enemies) {
                $this->log->write("Randomizing enemies on level " . $level->name . "\n");
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
                                $new_object = 0;
                                if ($o == Enemy::get('Toad')) {
                                    $z = count($this->enemy_pools->toad_pool);
                                    $new_object = $this->enemy_pools->toad_pool[mt_rand(0, count($this->enemy_pools->toad_pool) - 1)];
                                    $new_coord = 0xc8;
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
                                $this->log->write("Changed an enemy to " . Enemy::getName($new_object) . "\n");
                            }
                        } else {
                            $new_data = 0;
                            if (mt_rand(1, 100) <= $percentage) {
                                if ($o == Enemy::get('Toad')) {
                                    $new_object = $this->enemy_pools->toad_pool[mt_rand(0, count($this->enemy_pools->toad_pool) - 1)];
                                    $new_coord = 0xc8;
                                    $game->addData($offset + $i, pack('C*', $new_coord));
                                } else if ($o == Enemy::get('Bowser Fire Generator') or $o == Enemy::get('Red Flying Cheep-Cheep Generator') or $o == Enemy::get('Bullet Bill/Cheep-Cheep Generator')) {
                                    // TODO: should Bowser Fire Generator be included in this?
                                    $new_object = $this->enemy_pools->generator_pool[mt_rand(0, count($this->enemy_pools->generator_pool) - 1)];
                                } else {
                                    $new_object = $this->enemy_pools->reasonable_enemy_pool[mt_rand(0, count($this->enemy_pools->reasonable_enemy_pool) - 1)];
                                }

                                $new_data = (($p | $h) | $new_object);
                                $game->addData($offset + $i + 1, pack('C*', $new_data));
                                $this->log->write("Changed an enemy to " . Enemy::getName($new_object) . "\n");
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
        $all_levels = [
            '1-1', '1-2', '1-3', '1-4', '2-1', '2-2', '2-3', '2-4', '3-1', '3-2', '3-3', '3-4', '4-1', '4-2', '4-3', '4-4',
            '5-1', '5-2', '5-3', '5-4', '6-1', '6-2', '6-3', '6-4', '7-1', '7-2', '7-3', '7-4', '8-1', '8-2', '8-3', '8-4',
        ];

        $shuffledlevels = mt_shuffle($all_levels);
        //print_r($shuffledlevels);

        $lastlevelindex = 0;
        $levelindex = 0;
        $worldindex = 0;
        $shuffleindex = 0;

        // TODO: reduce code duplication!
        if ($this->options['pipeTransitions'] == 'remove') {
            for ($i = 0; $i < count($shuffledlevels); $i++) {

                // Select next level in list of shuffled levels, set its data
                $game->worlds[$worldindex]->levels[$levelindex] = Level::get($shuffledlevels[$shuffleindex]);
                $game->worlds[$worldindex]->levels[$levelindex]->world_num = $worldindex;

                // Is this level a castle?
                if (Level::get($shuffledlevels[$shuffleindex])->map >= 0x60 and Level::get($shuffledlevels[$shuffleindex])->map <= 0x65) {
                    // it's a castle, so increase the world index and reset the level index
                    $worldindex++;
                    if ($worldindex > 7) {
                        $worldindex = 7;
                    }

                    $levelindex = -1;
                }

                $lastlevelindex = $levelindex;
                $levelindex++;
                $shuffleindex++;
            }
        } else if ($this->options['pipeTransitions'] == 'keep') {
            // This part is a mess
            for ($i = 0; $i < count($shuffledlevels); $i++) {
                $game->worlds[$worldindex]->levels[$levelindex] = Level::get($shuffledlevels[$shuffleindex]);
                $game->worlds[$worldindex]->levels[$levelindex]->world_num = $worldindex;

                if (Level::get($shuffledlevels[$shuffleindex])->map >= 0x60 and Level::get($shuffledlevels[$shuffleindex])->map <= 0x65) {
                    // it's a castle, so increase the world index and reset the level index
                    $worldindex++;
                    if ($worldindex > 7) {
                        $worldindex = 7;
                    }

                    $levelindex = -1;
                }

                if ($shuffleindex < 30) {
                    if (in_array(Level::get($shuffledlevels[$shuffleindex + 1])->name, ['1-2', '2-2', '4-2'])) {
                        $levelindex++;
                        $game->worlds[$worldindex]->levels[$levelindex] = Level::get('Pipe Transition');
                    }
                }

                $levelindex++;
                $shuffleindex++;
            }
        }
    }

    /*
     * Shuffle World Order - produce a seed where the worlds and their levels stay vanilla,
     * but the order the worlds appear in is shuffled.
     * World 8 will always be last.
     */
    public function shuffleWorldOrder(&$game)
    {
        $worlds = [0, 1, 2, 3, 4, 5, 6];
        $shuffledworlds = mt_shuffle($worlds);

        // shuffle worlds 1-7
        for ($i = 0; $i <= 6; $i++) {
            switch ($shuffledworlds[$i]) {
                case 0:
                    if ($this->options['pipeTransitions'] == 'keep') {
                        $game->worlds[$i] = new World1($i + 1);
                    }

                    if ($this->options['pipeTransitions'] == 'remove') {
                        $game->worlds[$i] = new World1NoPipeTransition($i + 1);
                    }

                    break;
                case 1:
                    if ($this->options['pipeTransitions'] == 'keep') {
                        $game->worlds[$i] = new World2($i + 1);
                    }

                    if ($this->options['pipeTransitions'] == 'remove') {
                        $game->worlds[$i] = new World2NoPipeTransition($i + 1);
                    }

                    break;
                case 2:
                    $game->worlds[$i] = new World3($i + 1);
                    break;
                case 3:
                    if ($this->options['pipeTransitions'] == 'keep') {
                        $game->worlds[$i] = new World4($i + 1);
                    }

                    if ($this->options['pipeTransitions'] == 'remove') {
                        $game->worlds[$i] = new World4NoPipeTransition($i + 1);
                    }

                    break;
                case 4:
                    $game->worlds[$i] = new World5($i + 1);
                    break;
                case 5:
                    $game->worlds[$i] = new World6($i + 1);
                    break;
                case 6:
                    $game->worlds[$i] = new World7($i + 1);
                    break;
            }
        }

        // set world 8 as world 8
        $game->worlds[7] = new World8(8);
        // set the shuffled worlds to their vanilla layout
        $game->setVanillaWorldData();
        // TODO: Set world_num if we need it for sanity checks!
        //print_r($game);
    }

    /*
     * Shuffle levels, but make sure each -4 is a castle.
     * Castles are also shuffled, except the 8-4 which is 8-4 (handled by sanity check)
     * TODO: add keeping pipe transitions in place.
     */
    public function shuffleLevelsWithNormalWorldLength(&$game)
    {
        $levels = [
            '1-1', '1-2', '1-3', '2-1', '2-2', '2-3', '3-1', '3-2', '3-3', '4-1', '4-2', '4-3',
            '5-1', '5-2', '5-3', '6-1', '6-2', '6-3', '7-1', '7-2', '7-3', '8-1', '8-2', '8-3',
        ];
        $castles = ['1-4', '2-4', '3-4', '4-4', '5-4', '6-4', '7-4', '8-4'];

        $shuffledlevels = mt_shuffle($levels);
        $shuffledcastles = mt_shuffle($castles);

        if ($this->options['pipeTransitions'] == 'remove') {
            $this->log->write("Removing pipe transitions\n");
            $levelindex = 0;
            $castleindex = 0;
            for ($w = 0; $w < 8; $w++) {
                for ($i = 0; $i < 3; $i++) {
                    $game->worlds[$w]->levels[$i] = Level::get($shuffledlevels[$levelindex]);
                    $levelindex++;
                }
                if ($castleindex < 8) {
                    $game->worlds[$w]->levels[3] = Level::get($shuffledcastles[$castleindex]);
                }

                $castleindex++;
            }
        } else if ($this->options['pipeTransitions'] == 'keep') {
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

    public function sanityCheckWorldLayout(&$game)
    {
        // Check that 8-4 is the last level
        if ($game->worlds[7]->levels[count($game->worlds[7]->levels) - 1] != Level::get('8-4')) {
            $this->log->write("Sanity check fail: 8-4 is not the last level!\n");
            return false;
        }

        // Theory: pipes get messed up if 1-1 and 2-1 are in the same world
        // So let's avoid that
        foreach ($game->worlds as $world) {
            if ($world->hasLevel('1-1') && $world->hasLevel('2-1')) {
                $this->log->write("Sanity check fail: 1-1 and 2-1 are in the same world!\n");
                return false;
            }
        }

        /*
         * Easy fix for the Warp Zone Conundrum:
         * When changing warp zone destinations in any way, we have to
         * force 1-2 to be in world 1, and 4-2 to not be in world 1
         * Making changed warp destinations work with any layout
         * requires changes/additions to the game code that I (so far)
         * haven't been able to figure out how to do
         *
         * TODO: even if we don't randomize warp zones, we need to have 1-2 in world 1 and 4-2 in world > 1
         * because otherwise it can get weird.
         *
         * TODO: can the randomizer write custom code with knowledge of
         * which world has 1-2/4-2?
         *
         * TODO: uhm, we need to do this even if we're not shuffling warp zones, don't we?
         * Or do we just leave that be?
         * If we do, then warp zones can get a bit strange, but as long as that's communicated
         * to the player that might be ok?
         */
        if ($this->options['warpZones'] != "normal") {
            foreach ($game->worlds as $world) {
                if ($world->hasLevel('1-2') && $world->num != 1) {
                    $this->log->write("Sanity check fail: 1-2 is not in world 1!\n");
                    return false;
                }
            }

            // If we pass the previous test, now check 4-2
            foreach ($game->worlds as $world) {
                if ($world->hasLevel('4-2') && $world->num == 1) {
                    $this->log->write("Sanity check fail: 4-2 is in world 1!\n");
                    return false;
                }
            }
        }

        return true;
    }

    public function randomizeBowserAbilities(&$game)
    {
        $this->log->write("Randomizing Bowser's abilities...\n");

        $new_hammer_world = mt_rand(0, 6);
        $new_fire_world = mt_rand($new_hammer_world, 7);
        //print("Hammer: $new_hammer_world\nFire: $new_fire_world\n");

        $game->addData(HammerTimeOffset, pack('C*', $new_hammer_world));
        $game->addData(HammerTimeOffset2, pack('C*', $new_hammer_world));
        $game->addData(FireTimeOffset, pack('C*', $new_fire_world));
    }

    public function randomizeBowserHitpoints(&$game)
    {
        $this->log->write("Randomizing Bowser's hitpoints...\n");

        if ($this->options['bowserHitpoints'] == "easy") {
            $new_hitpoints = mt_rand(1, 5);
        } else if ($this->options['bowserHitpoints'] == "medium") {
            $new_hitpoints = mt_rand(5, 10);
        } else if ($this->options['bowserHitpoints'] == "hard") {
            $new_hitpoints = mt_rand(10, 20);
        } else if ($this->options['bowserHitpoints'] == "random") {
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

        if ($this->options['startingLives'] == "easy") {
            $new_lives = mt_rand(7, 10);
        } else if ($this->options['startingLives'] == "medium") {
            $new_lives = mt_rand(4, 6);
        } else if ($this->options['startingLives'] == "hard") {
            $new_lives = mt_rand(1, 3);
        } else if ($this->options['startingLives'] == "very-hard") {
            $new_lives = 1;
        } else if ($this->options['startingLives'] == "random") {
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
        $offset = WarpZonesOffset;

        if ($this->options['warpZones'] == 'random') {
            $this->log->write("Randomizing Warp Zones...\n");
            for ($i = 0; $i < 11; $i++) {
                $new_warp = mt_rand(1, 8);
                $game->addData($offset + $i, pack('C*', $new_warp));
                if ($i != 3 && $i != 4 && $i != 6 && $i != 7) {
                    $this->log->write("Warp pipe randomized to $new_warp\n");
                }
            }
            // cheap and easy fix
            $new_warp = 0x00;
            $game->addData(0x805, pack('C*', $new_warp));
            $game->addData(0x809, pack('C*', $new_warp));
            $new_warp = 0x24;
            $game->addData(0x806, pack('C*', $new_warp));
            $game->addData(0x808, pack('C*', $new_warp));
        } else if ($this->options['warpZones'] == 'shuffle') {
            $this->log->write("Shuffling Warp Pipes...\n");
            $destinations = [2, 3, 4, 5, 6, 7, 8]; // 0x24 is "blank"
            $shuffled_destinations = mt_shuffle($destinations);
            $index = 0;
            $game->addData($offset, pack('C*', $shuffled_destinations[0]));
            $game->addData($offset + 1, pack('C*', $shuffled_destinations[1]));
            $game->addData($offset + 2, pack('C*', $shuffled_destinations[2]));
            $game->addData($offset + 5, pack('C*', $shuffled_destinations[3]));
            $game->addData($offset + 8, pack('C*', $shuffled_destinations[4]));
            $game->addData($offset + 9, pack('C*', $shuffled_destinations[5]));
            $game->addData($offset + 10, pack('C*', $shuffled_destinations[6]));
            $this->log->write("Warp pipes shuffled to ");
            for ($i = 0; $i < 7; $i++) {
                $this->log->write($shuffled_destinations[$i] . " ");
            }
            $this->log->write("\n");
        } else if ($this->options['warpZones'] == 'useful') {
            /*
            We have 3 warp zones available
            a) One in 1-2
            b) One in 4-2 (via beanstalk)
            c) One in 4-2 (accessed like 1-2)

            In the future, b) can be randomized so that it's accessed from a pipe or beanstalk elsewhere.
            So we need to look at the game object, and find what world each warp zone is in.
            Then, for each warp zone:
            - get 3 random numbers that are > world number and <= 8
            - apply those numbers to the correct offset in rom
             */

            // TODO: we could make sure there are 3 different destinations - when possible
            // it's a bit silly when all pipes lead to world 8.....

            /*
             * disasm line 3566
             * BIG PROBLEM!!!!: game expects one warp zone in world 1, the others in a later world
             * I can't find a reliable way to check what the current map is
             * Therefore we need to either
             * a) force 1-2 to be in world 1 and force 4-2 to not be in world 1
             * or
             * b) change something in the game code
             * or
             * c) write the warp zone table differently
             *
             * because (right now) 1-2 and 4-2 could be anywhere
             *
             * EASY SOLUTION:
             * force 1-2 to be in world 1
             * IDEA: warps in 1-2 will not take your further than what world 4-2 is in
             * so like mt_rand($world->num + 1, world4->num)
             *
             */
            foreach ($game->worlds as $world) {
                foreach ($world->levels as $level) {
                    if ($level->name == '1-2') {
                        for ($i = 0; $i < 3; $i++) {
                            $new_warp = mt_rand($world->num + 1, 8);
                            $game->addData($offset + $i, pack('C*', $new_warp));
                            $this->log->write("Warp pipe in 1-2 (world $world->num) randomized to $new_warp\n");
                        }
                    }
                    if ($level->name == '4-2') {
                        // area accessed by beanstalk
                        for ($i = 8; $i < 11; $i++) {
                            $new_warp = mt_rand($world->num + 1, 8);
                            $game->addData($offset + $i, pack('C*', $new_warp));
                            $this->log->write("Warp pipe in 4-2 (beanstalk area) (world $world->num) randomized to $new_warp\n");
                        }
                        // area at end of level (only one pipe there)
                        $new_warp = mt_rand($world->num + 1, 8);
                        $game->addData($offset + 5, pack('C*', $new_warp));
                        $this->log->write("Warp pipe in 4-2 (end of level) (world $world->num) randomized to $new_warp\n");
                    }
                }
            }
        } else if ($this->options['warpZones'] == 'gamble') {
            if ($this->options['hiddenWarpDestinations'] != 'true') {
                print("warp zone gamble requires hidden warp destinations set to true!\n");
                exit(1);
            }
            // warp zones in 1-2 and 4-2 will have 2 guaranteed good pipes, 1 guaranteed bad.
            // TODO: less code duplication
            foreach ($game->worlds as $world) {
                foreach ($world->levels as $level) {
                    if ($level->name == '1-2') {
                        $good_warp[0] = mt_rand($world->num + 1, 8);
                        $good_warp[1] = mt_rand($world->num + 1, 8);
                        $bad_warp = mt_rand(1, $world->num);
                        // make sure we shuffle the order so you can't know which pipe is which
                        $shuffled = mt_shuffle([$good_warp[0], $good_warp[1], $bad_warp]);

                        $offset = WarpZonesOffset;
                        for ($i = 0; $i < 3; $i++) {
                            $game->addData($offset + $i, pack('C*', $shuffled[$i]));
                            $this->log->write("Warp pipe in 1-2 (world $world->num) randomized to $shuffled[$i]\n");
                        }
                    }
                    if ($level->name == '4-2') {
                        // area accessed by beanstalk
                        $good_warp[0] = mt_rand($world->num + 1, 8);
                        $good_warp[1] = mt_rand($world->num + 1, 8);
                        $bad_warp = mt_rand(1, $world->num);
                        $shuffled = mt_shuffle([$good_warp[0], $good_warp[1], $bad_warp]);

                        $offset = WarpZonesOffset + 8;
                        for ($i = 0; $i < 3; $i++) {
                            $game->addData($offset + $i, pack('C*', $shuffled[$i]));
                            $this->log->write("Warp pipe in 4-2 (beanstalk area) (world $world->num) randomized to $shuffled[$i]\n");
                        }

                        // warp zone at end of 4-2 will be 50/50 good or bad
                        $chance = mt_rand(1, 100);
                        if ($chance <= 50) {
                            $new_warp = mt_rand($world->num + 1, 8);
                        } else {
                            $new_warp = mt_rand(1, $world->num);
                        }
                        $game->addData($offset + 5, pack('C*', $new_warp));
                        $this->log->write("Warp pipe in 4-2 (end of level) (world $world->num) randomized to $new_warp\n");
                    }
                }
            }

        } else {
            echo "Invalid value for option Warp Zones!";
            exit(1);
        }
    }

    public function removeWarpZoneLabels(&$game)
    {
        // This takes the routine which prints the warp pipe destination above each pipe,
        // and replaces it with NOP instructions, thus no pipe destination labels get printed on screen,
        // but the pipes still function normally.
        $offset = 0x892;
        for ($i = 0; $i < 22; $i++) {
            $game->addData($offset + $i, pack('C*', 0xEA));
        }
    }

    public function randomizeFireworks(&$game)
    {
        $this->log->write("Randomizing fireworks...\n");
        $digits = [1, 2, 3, 4, 5, 6, 7, 8, 9];
        $random_digits = mt_shuffle($digits);

        // use indexes 1, 3, 6 just as a nod to the original game :)
        $game->addData(FireworksOffset1, pack('C*', $random_digits[1]));
        $game->addData(FireworksOffset2, pack('C*', $random_digits[3]));
        $game->addData(FireworksOffset3, pack('C*', $random_digits[6]));
        $this->log->write("Fireworks will appear when last digit of timer is " . $random_digits[1] . ", " . $random_digits[3] . " or " . $random_digits[6] . "\n");
    }

    public function fixPipes(Game &$game)
    {
        $levels = ['4-1', '1-2', '2-1', '1-1', '3-1', '4-1', '4-2', '5-1', '5-2', '6-2', '7-1', '8-1', '8-2', '2-2', '7-2'];
        $this->log->write("Fixing Pipes...\n");
        foreach ($game->worlds as $world) {
            // print_r($world);
            foreach ($world->levels as $level) {
                if (in_array($level->name, $levels)) {
                    if ($level->pipe_pointers) {
                        foreach ($level->pipe_pointers as list($entry, $exit)) {
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
                            $this->log->write("Fixing pipe in " . $level->name . " - New world is " . $new_world . "\n");
                            //$this->log->write(sprintf("ROM entry: %04x  exit: %04x\n", $entry, $exit));
                            //$this->log->write(sprintf("Old entry: %02x  Old exit: %02x\n", $entry_data, $exit_data));
                            //$this->log->write(sprintf("New entry: %02x  New exit: %02x\n", $new_entry_data, $new_exit_data));
                        }
                    }
                }
            }
        }
    }

    public function fixMidwayPoints(Game &$game)
    {
        $this->log->write("Fixing midway points:\n");

        if (($this->options['shuffleLevels'] == 'all' && $this->options['normalWorldLength'] == 'false')) {
            // Remove midway points
            $this->log->write("Removing all midway points.\n");
            for ($i = 0; $i < 0xF; $i++) {
                $game->midway_points[$i] = 0x00;
            }
        }

        if (($this->options['shuffleLevels'] == 'all' && $this->options['normalWorldLength'] == 'true') ||
            ($this->options['shuffleLevels'] == 'worlds')) {
            // Fix midway points
            $this->log->write("Moving midway points around to correct positions.\n");
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
        $this->log->write("Writing Seedhash on title screen...\n");

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

    public function getFlags($options)
    {
        $flags[0] = $options['pipeTransitions'][2];
        $flags[1] = $options['shuffleLevels'][1];
        $flags[1]++;
        $flags[1]++;
        $flags[2] = $options['normalWorldLength'][1];
        $flags[2]++;
        $flags[2]++;
        $flags[3] = $options['enemies'][12];
        $flags[4] = $options['blocks'][11];
        $flags[4]++;
        $flags[4]++;
        $flags[5] = $options['bowserAbilities'][3];
        $flags[6] = $options['bowserHitpoints'][0];
        $flags[6]++;
        $flags[7] = $options['startingLives'][0];
        $flags[7]++;
        $flags[7]++;
        $flags[8] = $options['warpZones'][2];
        $flags[8]++;
        $flags[8]++;
        $flags[8]++;
        $flags[9] = $options['hiddenWarpDestinations'][3];
        $flags[9]++;
        $flags[9]++;
        $flags[9]++;
        $flags[10] = $options['fireworks'][3];
        $flags[10]--;
        $flags[10]--;
        $flags[10]--;

        $s = implode("", $flags);
        $f = strtoupper($s);

        return $f;
    }

    public function makeFlags()
    {
        $this->flags = $this->getFlags($this->options);
        //print("Flags: $this->flags\n");
    }

    public function makeSeedHash()
    {
        // TODO - thought/idea:
        // include the MD5 of the randomized ROM in the seedhash?
        // - it would have to be the MD5 of the file BEFORE writing the seedhash though.
        // - but is it necessary? probably not. it could be an extra guarantee that the
        // files are identical, but there shouldn't be any risk of collisions, right?
        $hashstring = $this->flags . strval($this->getSeed() . \SMBR\Randomizer::VERSION . $this->rom->getMD5());
        $this->seedhash = hash("crc32b", $hashstring);

        // print("SeedHash: $this->seedhash\n");
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

        // Set up 8 worlds in game structure
        $game->resetWorlds();

        // print("\nHere we go! Making randomized SMB ROM with seed $this->rng_seed\n");
        $this->log->write("SMB Randomizer v" . self::VERSION . "\n");
        $this->log->write("Seed: " . $this->rng_seed . "\n");
        $this->log->write("Flags: " . $this->flags . "\n");
        $this->log->write("Seedhash: " . strtoupper($this->seedhash) . "\n\n");
        $this->log->write("OPTIONS:\n");
        $this->printOptionsToLog();
        $this->log->write("\nStarting randomization...\n");

        //  Shuffle Levels
        // This part can be optimized / written less strangely. Callback functions seems like a good idea.
        if ($this->options['shuffleLevels'] == "all") {
            if ($this->options['normalWorldLength'] == "true") {
                $this->log->write("Shuffling all levels (normal world length)...\n");
                $this->shuffleLevelsWithNormalWorldLength($game);
                while (!$this->sanityCheckWorldLayout($game)) {
                    //$this->log->write("World Layout sanity check failed! Reshuffling...\n");
                    $game->resetWorlds();
                    $this->shuffleLevelsWithNormalWorldLength($game);
                }
            } else {
                // any world length
                $this->log->write("Shuffling all levels (varying world length)...\n");
                $this->shuffleAllLevels($game);
                while (!$this->sanityCheckWorldLayout($game)) {
                    //$this->log->write("World Layout sanity check failed! Reshuffling...\n");
                    $game->resetWorlds();
                    $this->shuffleAllLevels($game);
                }
            }
        } else if ($this->options['shuffleLevels'] == "worlds") {
            $this->log->write('Shuffling world order...');
            $this->shuffleWorldOrder($game);
            while (!$this->sanityCheckWorldLayout($game)) {
                //$this->log->write("World Layout sanity check failed! Reshuffling...\n");
                $game->resetWorlds();
                $this->shuffleWorldOrder($game);
            }
            $game->setVanillaWorldData();
        } else if ($this->options['shuffleLevels'] == "none") {
            $game->setVanillaWorldData();
            while (!$this->sanityCheckWorldLayout($game)) {
                $this->log->write("Vanilla World Layout sanity check failed?!! In a way that shouldn't happen...\n");
                exit(1);
            }
        } else {
            print("Unrecognized option " . $this->options['shuffleLevels'] . " for Shuffle Levels! Exiting...");
            exit(1);
        }

        //  Shuffle Enemies
        if ($this->options['enemies'] == "randomizeFull") {
            $this->randomizeEnemies($game, false);
        } else if ($this->options['enemies'] == "randomizePools") {
            $this->randomizeEnemies($game, true);
        } else if ($this->options['enemies'] == "randomizeNone") {
            $this->log->write("No randomization of enemies!\n");
        }

        // Shuffle Blocks
        if ($this->options['blocks'] == "randomizeAll") {
            $this->randomizeBlocks($game, $item_pools->all_items, $item_pools->all_items);
        } else if ($this->options['blocks'] == "randomizePowerups") {
            $this->randomizeBlocks($game, $item_pools->powerups, $item_pools->powerups);
        } else if ($this->options['blocks'] == "randomizeGrouped") {
            $this->randomizeBlocks($game, $item_pools->all_question_blocks, $item_pools->all_question_blocks);
            $this->randomizeBlocks($game, $item_pools->all_hidden_blocks, $item_pools->all_hidden_blocks);
            $this->randomizeBlocks($game, $item_pools->all_brick_blocks, $item_pools->all_brick_blocks);
        } else if ($this->options['blocks'] == "randomizeCoins") {
            $this->randomizeBlocks($game, $item_pools->all_items, $item_pools->all_coins);
        } else if ($this->options['blocks'] == "randomizeNone") {
            $this->log->write("No randomization of blocks!\n");
        }

        // Randomize Bowser's Abilities
        if ($this->options['bowserAbilities'] == "true") {
            $this->randomizeBowserAbilities($game);
        }

        // Randomize Bowser's Hitpoints
        if ($this->options['bowserHitpoints'] != "normal") {
            $this->randomizeBowserHitpoints($game);
        }

        // Randomize player's starting lives
        if ($this->options['startingLives'] != "normal") {
            $this->randomizeStartingLives($game);
        }

        // Randomize warp zones
        if ($this->options['warpZones'] != "normal") {
            $this->randomizeWarpZones($game);
        }

        // Remove warp zone destination text if selected
        if ($this->options['hiddenWarpDestinations'] == "true") {
            $this->removeWarpZoneLabels($game);
        }

        if ($this->options['fireworks'] == "true") {
            $this->randomizeFireworks($game);
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

        // write "pretty" world layout to logfile
        $this->log->write($game->prettyprint());

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
