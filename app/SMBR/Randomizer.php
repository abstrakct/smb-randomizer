<?php

namespace App\SMBR;

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

use App\SMBR\Colorscheme;
use App\SMBR\Flagstring;
use App\SMBR\Game;
use App\SMBR\ItemPools;
use App\SMBR\Level;
use App\SMBR\Translator;

// TODO: move these somewhere better!
const HammerTimeOffset    = 0x512b;
const HammerTimeOffset2   = 0x5161;
const FireTimeOffset      = 0x515d;
const BowserHPOffset      = 0x457c;
const StartingLivesOffset = 0x107a;
const WarpZonesOffset     = 0x0802;
const WarpZone12Offset    = 0x0802;
const WarpZone42SkyOffset = 0x080a;
const WarpZone42EndOffset = 0x0807;
const FireworksOffset1    = 0x5308;
const FireworksOffset2    = 0x530e;
const FireworksOffset3    = 0x5314;

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
    public $flagstring;
    public $seedhash;
    protected $rng_seed;
    protected $seed;
    protected $options;
    protected $rom;
    private $trans;
    private $log;
    // TODO: move all enemy data to Enemy class
    public $enemy_pools;
    public $item_pools;
    const VERSION = "0.9.8 beta";

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
        $this->rng_seed     = $seed;
        $this->options      = $opt;
        $this->rom          = $rom;
        $this->flagstring   = new Flagstring($opt);
        $this->trans        = new Translator();
        $this->enemy_pools  = new \App\SMBR\EnemyPools();
        $this->item_pools   = new \App\SMBR\ItemPools();
        $this->colorschemes = array('random' => new Colorscheme(0, 0, 0),
            'Vanilla Mario'                      => new Colorscheme(0x16, 0x27, 0x18),
            'Vanilla Luigi'                      => new Colorscheme(0x30, 0x27, 0x19),
            'Vanilla Fire'                       => new Colorscheme(0x37, 0x27, 0x16),
            'Pale Ninja'                         => new Colorscheme(0xce, 0xd0, 0x1e),
            'All Black'                          => new Colorscheme(0x8d, 0x8d, 0x8d),
            'Black & Blue'                       => new Colorscheme(0xcc, 0x18, 0x2f),
            'Black & Blue 2'                     => new Colorscheme(0x51, 0xf8, 0x6e),
            'Denim'                              => new Colorscheme(0x80, 0xa7, 0xcc),
            'Mustard Man'                        => new Colorscheme(0xd8, 0x27, 0x28),
            'Pretty In Pink'                     => new Colorscheme(0xe3, 0xb2, 0x14),
            'Outrun'                             => new Colorscheme(0x61, 0x27, 0x94),
            'Outrun 2'                           => new Colorscheme(0x94, 0x27, 0x61),
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

    public function setOptions($opt)
    {
        $this->options = $opt;
    }

    public function getOptions()
    {
        return $this->options;
    }

    public function setOptionsFromFlagstring($flag_string)
    {
        $this->flagstringToOptions($flag_string, $this->options);
    }

    public function setMarioColorScheme(string $colorscheme, Game &$game)
    {
        $this->log->write("Mario Color Scheme: " . $colorscheme . "\n");
        if ($colorscheme == "random") {
            $this->setSeed();
            $outer = mt_rand(0, 255);
            $skin  = mt_rand(0, 255);
            $inner = mt_rand(0, 255);
        } else if ($colorscheme == "clothes") {
            $this->setSeed();
            $outer = mt_rand(0, 255);
            $skin  = 0x27;
            $inner = mt_rand(0, 255);
        } else {
            $outer = $this->colorschemes[$colorscheme]->outer;
            $skin  = $this->colorschemes[$colorscheme]->skin;
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
            $skin  = mt_rand(0, 255);
            $inner = mt_rand(0, 255);
        } else if ($colorscheme == "clothes") {
            $this->setSeed();
            $outer = mt_rand(0, 255);
            $skin  = 0x27;
            $inner = mt_rand(0, 255);
        } else {
            $outer = $this->colorschemes[$colorscheme]->outer;
            $skin  = $this->colorschemes[$colorscheme]->skin;
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
            $skin  = mt_rand(0, 255);
            $inner = mt_rand(0, 255);
        } else if ($colorscheme == "clothes") {
            $this->setSeed();
            $outer = mt_rand(0, 255);
            $skin  = 0x27;
            $inner = mt_rand(0, 255);
        } else {
            $outer = $this->colorschemes[$colorscheme]->outer;
            $skin  = $this->colorschemes[$colorscheme]->skin;
            $inner = $this->colorschemes[$colorscheme]->inner;
        }
        $this->rom->setLuigiOuterColor($outer, $game);
        $this->rom->setLuigiSkinColor($skin, $game);
        $this->rom->setLuigiInnerColor($inner, $game);
    }

    /*
     * A bit of circular logic here, but for the "controlled" mode,
     * we can select a level where SHM starts, and then make the level shuffle conform to
     * that.
     *
     * 2019-04-11
     * Also, it appears that we might have to force certain levels to be after start
     * of secondary hard mode, e.g. 7-4
     * ???
     * Solution: disable this stuff for now!???
     */
    public function randomizeSecondaryHardModeStart(&$game)
    {
        $world_offset = 0x104b;
        $level_offset = 0x1054;
        $this->log->write("Randomizing where secondary hard mode starts...\n");

        $new_world = mt_rand(0, 7);
        $new_level = mt_rand(0, count($game->worlds[$new_world]->levels) - 1);

        $this->log->write("New start for secondary hard mode: World $new_world Level $new_level\n");

        $game->addData($world_offset, pack('C*', $new_world));
        $game->addData($level_offset, pack('C*', $new_level));
    }

    public function randomizeEnemiesChaos(&$game)
    {
        $this->log->write("Randomizing enemies: CHAOS!\n");
        if ($this->options['hardMode'] == 'always') {
            $this->log->write("Secondary hard mode will be activated for all levels.\n");
        } else if ($this->options['hardMode'] == 'vanilla') {
            $this->log->write("Secondary hard mode will be vanilla.\n");
        }
        $vanilla = Level::all();
        foreach ($vanilla as $level) {
            if ($level->has_enemies) {
                $this->log->write("Randomizing enemies on level " . $level->name . "\n");
                $this->randomizeEnemiesOnLevel($level->enemy_data_offset, $level->name, $game);
            }
        }
    }

    public function randomizeEnemiesControlled(&$game)
    {
        $this->log->write("Randomizing enemies: Controlled.\n");
        if ($this->options['hardMode'] == 'always') {
            $this->log->write("Secondary hard mode will be activated for all levels.\n");
        } else if ($this->options['hardMode'] == 'vanilla') {
            $this->log->write("Secondary hard mode will be vanilla.\n");
        }
        $vanilla = Level::all();
        foreach ($vanilla as $level) {
            if ($level->has_enemies) {
                $this->log->writeVerbose("RANDOMIZING ENEMIES ON LEVEL " . $level->name . "\n");
                $this->newRandomizeEnemiesOnLevel($level->enemy_data_offset, $game);
            }
        }
    }

    public function newRandomizeEnemiesOnLevel($offset, &$game)
    {
        $data                          = $this->rom->read($offset, 100);
        $end                           = 0;
        $level_has_cheepcheepgenerator = false;

        foreach ($data as $byte) {
            $end++;
            if ($byte == 0xFF) {
                break;
            }
        }

        for ($i = 0; $i < $end; $i += 2) {
            $x = $data[$i] & 0xF0;
            $y = $data[$i] & 0x0F;
            if ($y == 0xE) {
                $i++;
            } else if ($y > 0xE) {
                continue;
            } else {
                if ($data[$i] != 0xFF) {
                    // Let's randomize!
                    $do_randomize = true;
                    $p            = $data[$i + 1] & 0b10000000;
                    $h            = $data[$i + 1] & 0b01000000;
                    if ($this->options['hardMode'] == 'always') {
                        $h = 0;
                    }
                    $o = $data[$i + 1] & 0b00111111; // this is the enemy object

                    $this->log->writeVerbose("\tFound enemy: " . Enemy::getName($o) . "\n");

                    /* Some enemies can't be randomized, so let's check for those */
                    if (enemyIsInPool($o, $this->enemy_pools->dont_randomize)) {
                        $do_randomize = false;
                    }

                    if (in_array(($offset + $i), $this->enemy_pools->exceptions)) {
                        $this->log->writeVerbose("Found exception to enemy randomization!\n");
                        $do_randomize = false;
                    }

                    if ($this->options['excludeFirebars'] == 'true' && enemyIsInPool($o, $this->enemy_pools->firebar_pool)) {
                        $do_randomize = false;
                        $this->log->write("Fire Bar found, but excludeFirebars is set to true.\n");
                    }

                    if ($do_randomize) {
                        $new_candidates = $this->enemy_pools->new_pools[$o];

                        if (count($new_candidates) == 0) {
                            $this->log->write("ERROR: list of new candidates for enemy is empty!\n");
                            break;
                        }

                        $acceptable = false;

                        while (!$acceptable) {
                            $acceptable = true;
                            $new_enemy  = $new_candidates[mt_rand(0, count($new_candidates) - 1)];

                            if ($new_enemy == Enemy::get('Red Flying Cheep-Cheep Generator')) {
                                $level_has_cheepcheepgenerator = true;
                            }

                            if ($level_has_cheepcheepgenerator && $new_enemy == Enemy::get('Bowser Fire Generator')) {
                                $acceptable = false;
                                $this->log->writeVerbose("UNACCEPTABLE BOWSER FIRE GENERATOR GENERATED!!!\n");
                            }

                            if ($this->options['excludeFirebars'] == 'true' && enemyIsInPool($new_enemy, $this->enemy_pools->firebar_pool)) {
                                $acceptable = false;
                                $this->log->writeVerbose("Unacceptable Fire Bar generated! Retrying...\n");
                            }
                        }

                        $new_data = (($p | $h) | $new_enemy);
                        $game->addData($offset + $i + 1, pack('C*', $new_data));
                        $this->log->writeVerbose("\t\tChanged enemy to " . Enemy::getName($new_enemy) . "\n");

                        // Fix coordinates of some enemies
                        if ($new_enemy == Enemy::get('Green Cheep-Cheep (slow)') || $new_enemy == Enemy::get('Red Cheep-Cheep (fast)')) {
                            $yyy = mt_rand(0, 0xB);
                            $pos = $x | $yyy;
                            $game->addData($offset + $i, pack('C*', $pos));
                            $this->log->writeVerbose("\t\t\tChanged Y position to $yyy\n");
                        }

                        if ($new_enemy == Enemy::get('Lakitu')) {
                            $new_coord = 0xE2;
                            $game->addData($offset + $i, pack('C*', $new_coord));
                            $this->log->writeVerbose(sprintf("\t\t\tChanged coordinates to %02x\n", $new_coord));
                        }

                        if ($o == Enemy::get('Toad') && $new_enemy != Enemy::get('Toad')) {
                            $new_coord = $this->enemy_pools->toad_new_coords[$new_enemy];
                            $game->addData($offset + $i, pack('C*', $new_coord));
                            $this->log->writeVerbose(sprintf("\t\t\tChanged coordinates to %02x\n", $new_coord));
                        }
                    }
                }
            }
        }
    }

    // This is for the CHAOS randomization
    public function randomizeEnemiesOnLevel($offset, $level, &$game)
    {
        $end        = 0;
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
            $x            = $data[$i] & 0xf0;
            $y            = $data[$i] & 0x0f;
            if ($y == 0xE) {
                $i++;
            } else if ($y > 0xE) {
                continue;
            } else {
                if ($data[$i] != 0xFF) {
                    $p = $data[$i + 1] & 0b10000000;
                    $h = $data[$i + 1] & 0b01000000;
                    if ($this->options['hardMode'] == 'always') {
                        $h = 0;
                    }
                    $o = $data[$i + 1] & 0b00111111; // this is the enemy object

                    /* Some enemies can't be randomized, so let's check for those */
                    if (enemyIsInPool($o, $this->enemy_pools->dont_randomize)) {
                        $do_randomize = false;
                    }

                    if (in_array(($offset + $i), $this->enemy_pools->exceptions)) {
                        $this->log->writeVerbose("Found exception to enemy randomization!\n");
                        $do_randomize = false;
                    }

                    // TODO:
                    // if original enemy was firebar and new enemy is not firebar, set Y coord to one higher (-1).
                    // Will hopefully prevent enemies from being stuck inside the block where the firebar was
                    if ($do_randomize) {
                        $new_data   = 0;
                        $acceptable = false;

                        while (!$acceptable) {
                            if ($o == Enemy::get('Toad')) {
                                $new_object = $this->enemy_pools->toad_pool[mt_rand(0, count($this->enemy_pools->toad_pool) - 1)];
                                $new_coord  = 0xc8;
                                $game->addData($offset + $i, pack('C*', $new_coord));
                                $acceptable = true;
                            } else if (enemyIsInPool($o, $this->enemy_pools->generator_pool)) {
                                // TODO: should Bowser Fire Generator be included in this?
                                $new_object = $this->enemy_pools->generator_pool[mt_rand(0, count($this->enemy_pools->generator_pool) - 1)];
                                $acceptable = true;
                            } else {
                                if ($this->options['excludeFirebars'] == 'true') {
                                    $new_object = $this->enemy_pools->reasonable_enemy_pool_no_firebars[mt_rand(0, count($this->enemy_pools->reasonable_enemy_pool_no_firebars) - 1)];
                                    $acceptable = true;
                                } else {
                                    $new_object = $this->enemy_pools->reasonable_enemy_pool[mt_rand(0, count($this->enemy_pools->reasonable_enemy_pool) - 1)];
                                    $acceptable = true;

                                    // This doesn't always work because firebars can be in the floor or in the ceiling.
                                    // Possible solution: manually create a list of offsets for firebars in vanilla game,
                                    // and whether y should be increased or decreased

                                    //if (enemyIsInPool($o, $this->enemy_pools->firebar_pool) && !enemyIsInPool($new_object, $this->enemy_pools->firebar_pool)) {
                                    //    $new_y = $y - 2;
                                    //    $new_coord = ($x | $new_y);
                                    //    $game->addData($offset + $i, pack('C*', $new_coord));
                                    //}
                                }
                            }

                            if ($level == '1-2' && enemyIsInPool($new_object, $this->enemy_pools->generator_pool)) {
                                $acceptable = false;
                            }
                        }

                        $new_data = (($p | $h) | $new_object);
                        $game->addData($offset + $i + 1, pack('C*', $new_data));
                        $this->log->writeVerbose("Changed enemy: " . Enemy::getName($o) . " to " . Enemy::getName($new_object) . "\n");

                        if ($new_object == Enemy::get('Lakitu')) {
                            $new_coord = 0xE2;
                            $game->addData($offset + $i, pack('C*', $new_coord));
                            $this->log->writeVerbose(sprintf("\t\t\tChanged coordinates to %02x\n", $new_coord));
                        }
                    }
                }
            }
        }
    }

    public function randomizeSpinSpeed(&$game)
    {
        $offset = 0x445f;
        for ($i = 0; $i < 5; $i++) {
            $x = mt_rand(0x20, 0x60);
            $game->addData($offset + $i, pack('C*', $x));
        }
    }

    public function shuffleSpinDirections(&$game)
    {
        $offset   = 0x4464;
        $data     = [0x00, 0x00, 0x10, 0x10, 0x00];
        $new_data = mt_shuffle($data);

        for ($i = 0; $i < 5; $i++) {
            $game->addData($offset + $i, pack('C*', $new_data[$i]));
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

                $this->log->writeVerbose("Randomizing item blocks in $level->name\n");
                $end  = 0;
                $data = $this->rom->read($level->level_data_offset, 200);
                foreach ($data as $byte) {
                    $end++;
                    if ($byte == 0xFD) {
                        break;
                    }
                }

                for ($i = 2; $i < $end; $i += 2) {
                    $do_randomize = true;
                    $y            = $data[$i] & 0b00001111;
                    if ($y > 0x0B) {
                        $do_randomize = false;
                    }
                    if (in_array($level->level_data_offset + $i, $this->item_pools->exceptions)) {
                        $do_randomize = false;
                        $this->log->writeVerbose("Found Item Block randomization exception!\n");
                    }

                    if ($do_randomize) {
                        $p        = $data[$i + 1] & 0b10000000;
                        $object   = $data[$i + 1] & 0b01111111;
                        $new_data = 0x99;
                        if (in_array($object, $frompool)) {
                            $pull_key   = mt_rand(0, count($topool) - 1);
                            $new_object = $topool[$pull_key];
                            $new_data   = $p | $new_object;
                            $game->addData($level->level_data_offset + $i + 1, pack('C*', $new_data));
                            $this->log->writeVerbose("  Changed " . Item::getName($object) . " to " . Item::getName($new_object) . "\n");
                        }
                    }
                }
            }
        }
    }

    // Randomize the few blocks that exist in underground bonus areas
    //
    public function randomizeBlocksInUndergroundBonus(Game &$game, $frompool, $topool)
    {
        $this->log->write("Randomizing blocks in underground bonus areas!\n");

        $level = Level::get('underground-bonus');
        $end   = 0;
        $data  = $this->rom->read($level->level_data_offset, 200);

        foreach ($data as $byte) {
            $end++;
            if ($byte == 0xFD) {
                break;
            }
        }

        for ($i = 2; $i < $end; $i += 2) {
            $do_randomize = true;
            $y            = $data[$i] & 0b00001111;
            if ($y > 0x0B) {
                $do_randomize = false;
            }

            if ($do_randomize) {
                $p        = $data[$i + 1] & 0b10000000;
                $object   = $data[$i + 1] & 0b01111111;
                $new_data = 0x99;
                if (in_array($object, $frompool)) {
                    $pull_key   = mt_rand(0, count($topool) - 1);
                    $new_object = $topool[$pull_key];
                    $new_data   = $p | $new_object;
                    $game->addData($level->level_data_offset + $i + 1, pack('C*', $new_data));
                    $this->log->writeVerbose("  Changed " . Item::getName($object) . " to " . Item::getName($new_object) . "\n");
                }
            }
        }
    }
    /*
     * Shuffle levels, but castles can appear anywhere, except 8-4 which is always the last level.
     * Each castle represents the end of a world, but currently there are no restrictions on how
     * many levels can be in a world, except that there will be no more than 32 levels total like in vanilla.
     */
    public function shuffleAllLevels(&$game, $subset = false)
    {
        if ($subset) {
            $levels          = ['1-4', '2-4', '3-4', '4-4', '5-4', '6-4', '7-4', '8-4'];
            $possible_levels = [
                '1-1', '1-2', '1-3', '2-1', '2-2', '2-3', '3-1', '3-2', '3-3', '4-1', '4-2', '4-3',
                '5-1', '5-2', '5-3', '6-1', '6-2', '6-3', '7-1', '7-2', '7-3', '8-1', '8-2', '8-3',
            ];
            $num_levels = mt_rand(8, 24);

            $possible_shuffled = mt_shuffle($possible_levels);
            for ($i = 0; $i < $num_levels; $i++) {
                array_push($levels, $possible_shuffled[$i]);
            }

            // $levels += array_slice($possible_levels, 0, $num_levels);
            $all_levels = mt_shuffle($levels);
            // print_r($levels);
            // print_r($all_levels);
            $game->num_levels = $num_levels + 8;
        } else {
            $all_levels = [
                '1-1', '1-2', '1-3', '1-4', '2-1', '2-2', '2-3', '2-4', '3-1', '3-2', '3-3', '3-4', '4-1', '4-2', '4-3', '4-4',
                '5-1', '5-2', '5-3', '5-4', '6-1', '6-2', '6-3', '6-4', '7-1', '7-2', '7-3', '7-4', '8-1', '8-2', '8-3', '8-4',
            ];
        }

        $shuffledlevels = mt_shuffle($all_levels);
        //print_r($shuffledlevels);

        $levelindex   = 0;
        $worldindex   = 0;
        $shuffleindex = 0;

        // TODO: reduce code duplication!
        if ($this->options['pipeTransitions'] == 'remove') {
            for ($i = 0; $i < count($shuffledlevels); $i++) {
                // print("handling " . Level::get($shuffledlevels[$shuffleindex])->name . " worldindex = $worldindex levelindex = $levelindex\n");
                // Select next level in list of shuffled levels, set its data
                $game->worlds[$worldindex]->levels[$levelindex]            = Level::get($shuffledlevels[$shuffleindex]);
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

                $levelindex++;
                $shuffleindex++;
            }
        } else if ($this->options['pipeTransitions'] == 'keep') {
            for ($i = 0; $i < count($shuffledlevels); $i++) {
                $game->worlds[$worldindex]->levels[$levelindex]            = Level::get($shuffledlevels[$shuffleindex]);
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
        $worlds         = [0, 1, 2, 3, 4, 5, 6];
        $shuffledworlds = mt_shuffle($worlds);

        // shuffle worlds 1-7
        for ($i = 0; $i <= 6; $i++) {
            switch ($shuffledworlds[$i]) {
                case 0:
                    if ($this->options['pipeTransitions'] == 'keep') {
                        $game->worlds[$i] = new World1($i);
                    }

                    if ($this->options['pipeTransitions'] == 'remove') {
                        $game->worlds[$i] = new World1NoPipeTransition($i);
                    }

                    break;
                case 1:
                    if ($this->options['pipeTransitions'] == 'keep') {
                        $game->worlds[$i] = new World2($i);
                    }

                    if ($this->options['pipeTransitions'] == 'remove') {
                        $game->worlds[$i] = new World2NoPipeTransition($i);
                    }

                    break;
                case 2:
                    $game->worlds[$i] = new World3($i);
                    break;
                case 3:
                    if ($this->options['pipeTransitions'] == 'keep') {
                        $game->worlds[$i] = new World4($i);
                    }

                    if ($this->options['pipeTransitions'] == 'remove') {
                        $game->worlds[$i] = new World4NoPipeTransition($i);
                    }

                    break;
                case 4:
                    $game->worlds[$i] = new World5($i);
                    break;
                case 5:
                    $game->worlds[$i] = new World6($i);
                    break;
                case 6:
                    $game->worlds[$i] = new World7($i);
                    break;
            }
        }

        // set world 8 as world 8
        $game->worlds[7] = new World8(7);
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

        $shuffledlevels  = mt_shuffle($levels);
        $shuffledcastles = mt_shuffle($castles);

        if ($this->options['pipeTransitions'] == 'remove') {
            $levelindex  = 0;
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

    /*
     * Without making any other changes, these are the restraints we have to work within:
     * For exit pipes from the underground area,
     * page 0: 3 pipes
     * page 2: 2 pipes
     * page 4: 1 pipe
     * page 6: 2 pipes
     * page 8: 4 pipes
     * = 12 pipes
     *
     * On each page, each pipe needs to go to a different world than the other pipes in that page
     * We also have to change the entry pipe to match the exit pipe, obviously.
     *
     *
     * algorithm:
     * for each page
     *   - select the appropriate number n of entry pipes
     *   - try to find n pipes in different worlds
     *   - push to array, use in_array to check
     *   - if that's not possible, re-shuffle
     *
     *   OK SO
     * this algorithm now works, in that it selects some usable pipes for each page
     * and there are no collisions
     * So now all we need to do is set/write the correct new data
     *
     *   OK SO IT WORKS NOW :D
     *
     *   TODO: check that it works in all level shuffle modes!
     */
    public function shuffleUndergroundBonusAreaDestinations(&$game)
    {
        $this->log->write("Shuffling Underground Bonus Area Destinations...\n");

        $exitsByPage = [
            0 => [Pipe::get('1-1 Exit 1'), Pipe::get('2-1 Exit 1'), Pipe::get('7-1 Exit 1')],
            2 => [Pipe::get('1-2 Exit 1'), Pipe::get('8-1 Exit 1')],
            4 => [Pipe::get('3-1 Exit 1')],
            6 => [Pipe::get('4-1 Exit 1'), Pipe::get('6-2 Exit 2')],
            8 => [Pipe::get('4-2 Exit 1'), Pipe::get('5-1 Exit 1'), Pipe::get('6-2 Exit 1'), Pipe::get('8-2 Exit 1')],
        ];
        $pipeList  = [];
        $pageCount = [
            0 => 3,
            2 => 2,
            4 => 1,
            6 => 2,
            8 => 4,
        ];
        $newPipes = [];

        foreach ($game->worlds as $world) {
            foreach ($world->levels as $level) {
                if ($level->pipe_pointers) {
                    foreach ($level->pipe_pointers as list($entry, $exit)) {
                        if ($entry) {
                            if ($entry->getMap() == 0x42) {
                                array_push($pipeList, [$entry, $exit]);
                            }
                        }
                    }
                }
            }
        }

        // TODO: I'm pretty sure we need this, but should check that to be absolutely sure
        $this->fixPipes($game);

        for ($page = 0; $page <= 8; $page += 2) {
            $used_worlds = [];
            $done        = false;
            $n           = 0;
            $fail        = 0;
            while (!$done) {
                $key = mt_rand(0, count($pipeList) - 1);
                if (!in_array($pipeList[$key][0]->getWorldActive(), $used_worlds)) {
                    $used_worlds[]     = $pipeList[$key][0]->getWorldActive();
                    $newPipes[$page][] = $pipeList[$key];

                    // print info
                    $w = $pipeList[$key][0]->getWorldActive();
                    $this->log->writeVerbose("found usable pipe for page $page in world $w\n");

                    // end
                    $n++;
                    array_splice($pipeList, $key, 1);
                } else {
                    $fail++;
                    if ($fail > 100) {
                        $this->log->write("Failed more than 100 attempts - giving up...\n");
                        return false;
                    }
                }
                if ($n == $pageCount[$page]) {
                    $done = true;
                }
            }
        }

        // Set new data
        $this->log->writeVerbose("###\nSetting new data for pipes\n###\n");
        for ($page = 0; $page <= 8; $page += 2) {
            for ($i = 0; $i < $pageCount[$page]; $i++) {
                $entry = $newPipes[$page][$i][0];
                $entry->setPage($page);
                $originalExit = VanillaPipe::get($newPipes[$page][$i][1]->name);
                $newExit      = $exitsByPage[$page][$i];

                $newExit->setWorldActive($entry->getWorldActive());
                $newExit->setMap($originalExit->getMap());
                $newExit->setPage($originalExit->getPage());
                $newExit->setNewPageFlag($originalExit->getNewPageFlag());

                $this->log->writeVerbose("Original exit: {$originalExit->name}\n");
                $this->log->writeVerbose("New world active: {$newExit->getWorldActive()}\n");
                $this->log->writeVerbose("New map: {$newExit->getMap()}\n");
                $this->log->writeVerbose("New page: {$newExit->getPage()}\n");
            }
        }

        return true;
    }

    public function sanityCheckWorldLayout(&$game)
    {
        // Check that number of worlds == 8
        if (count($game->worlds) != 8) {
            $this->log->writeVerbose("Sanity check fail: Not 8 worlds in world layout!\n");
            return false;
        }

        // Check that number of levels == 32
        // TODO: 32 IS NOT ALWAYS CORRECT!!! because of pipe transitions etc
        // therefore: handle all variations here.
        $levels = 0;
        //if ($this->options["shuffleLevels"] == "none") {
        //    $num_levels = 35;
        //} else if ($this->options["shuffleLevels"] == "all" && $this->options["pipeTransitions"] == "keep") {
        //    $num_levels = 35;
        //} else {
        //    $num_levels = 32;
        //}

        foreach ($game->worlds as $world) {
            $levels += count($world->levels);
        }

        if ($levels != $game->num_levels) {
            $this->log->writeVerbose("Sanity check fail: Not $game->num_levels levels in world layout (levels = $levels)!\n");
            return false;
        }

        // Check that 8-4 is the last level
        if ($game->worlds[7]->levels[count($game->worlds[7]->levels) - 1] != Level::get('8-4')) {
            $this->log->writeVerbose("Sanity check fail: 8-4 is not the last level!\n");
            return false;
        }

        // Check for collisions if we are not shuffling underground bonus areas
        if ($this->options["shuffleUndergroundBonus"] == "false") {
            foreach ($game->worlds as $world) {
                if (
                    ($world->hasLevel('1-1') && $world->hasLevel('2-1')) ||
                    ($world->hasLevel('1-1') && $world->hasLevel('7-1')) ||
                    ($world->hasLevel('2-1') && $world->hasLevel('7-1')) ||
                    ($world->hasLevel('1-1') && $world->hasLevel('2-1') && $world->hasLevel('7-1'))
                ) {
                    $this->log->writeVerbose("Sanity check fail: 1-1 and/or 2-1 and/or 7-1 are in the same world!\n");
                    return false;
                }
            }

            foreach ($game->worlds as $world) {
                if (($world->hasLevel('1-2') && $world->hasLevel('8-1'))) {
                    $this->log->writeVerbose("Sanity check fail: 1-2 and 8-1 are in the same world!\n");
                    return false;
                }
            }

            foreach ($game->worlds as $world) {
                if (($world->hasLevel('4-1') && $world->hasLevel('6-2'))) {
                    $this->log->writeVerbose("Sanity check fail: 4-1 and 6-2 are in the same world!\n");
                    return false;
                }
            }

            foreach ($game->worlds as $world) {
                if (($world->hasLevel('4-2') && $world->hasLevel('5-1'))) {
                    $this->log->writeVerbose("Sanity check fail: 4-2 and 5-1 are in the same world!\n");
                    return false;
                }
            }

            foreach ($game->worlds as $world) {
                if (($world->hasLevel('6-2') && $world->hasLevel('5-1'))) {
                    $this->log->writeVerbose("Sanity check fail: 6-2 and 5-1 are in the same world!\n");
                    return false;
                }
            }

            foreach ($game->worlds as $world) {
                if (($world->hasLevel('8-2') && $world->hasLevel('5-1'))) {
                    $this->log->writeVerbose("Sanity check fail: 8-2 and 5-1 are in the same world!\n");
                    return false;
                }
            }

            foreach ($game->worlds as $world) {
                if (($world->hasLevel('6-2') && $world->hasLevel('8-2'))) {
                    $this->log->writeVerbose("Sanity check fail: 6-2 and 8-2 are in the same world!\n");
                    return false;
                }
            }

            foreach ($game->worlds as $world) {
                if (($world->hasLevel('4-2') && $world->hasLevel('6-2'))) {
                    $this->log->writeVerbose("Sanity check fail: 4-2 and 6-2 are in the same world!\n");
                    return false;
                }
            }

            foreach ($game->worlds as $world) {
                if (($world->hasLevel('4-2') && $world->hasLevel('8-2'))) {
                    $this->log->writeVerbose("Sanity check fail: 4-2 and 8-2 are in the same world!\n");
                    return false;
                }
            }
        } else {
            // Sanity checks for when we're shuffling underground bonus areas
            foreach ($game->worlds as $world) {
                // not sure why, but things break when 1-1 is in the same world as these. probably related to cloud areas in 5-2 and 6-2
                if (($world->hasLevel('1-1') && $world->hasLevel('5-2'))) {
                    $this->log->writeVerbose("Sanity check fail: 1-1 and 5-2 are in the same world! (shuffle underground = true)\n");
                    return false;
                }

                if (($world->hasLevel('1-1') && $world->hasLevel('6-2'))) {
                    $this->log->writeVerbose("Sanity check fail: 1-1 and 6-2 are in the same world! (shuffle underground = true)\n");
                    return false;
                }
            }
        }

        // Levels with cloud area 1 must be in different worlds
        // TODO: add option to include these areas (and water area) in shuffle
        // TODO: combine into one foreach loop
        foreach ($game->worlds as $world) {
            if (($world->hasLevel('5-2') && $world->hasLevel('2-1'))) {
                $this->log->writeVerbose("Sanity check fail: 5-2 and 2-1 are in the same world!\n");
                return false;
            }
        }

        // Levels with cloud area 2 must be in different worlds
        foreach ($game->worlds as $world) {
            if (($world->hasLevel('6-2') && $world->hasLevel('3-1'))) {
                $this->log->writeVerbose("Sanity check fail: 6-2 and 3-1 are in the same world!\n");
                return false;
            }
        }

        // Levels with pipe to water area must be in different worlds
        foreach ($game->worlds as $world) {
            if (($world->hasLevel('6-2') && $world->hasLevel('5-2'))) {
                $this->log->writeVerbose("Sanity check fail: 6-2 and 5-2 are in the same world!\n");
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
         * DONE: even if we don't randomize warp zones, we need to have 1-2 in world 1 and 4-2 in world > 1
         * because otherwise it can get weird.
         *
         * TODO: can the randomizer write custom code with knowledge of
         * which world has 1-2/4-2? probably yes, but we probably don't have the space unless we figure out
         * how to use a different mapper and get more PRGROM
         *
         * TODO: uhm, we need to do this even if we're not shuffling warp zones, don't we?
         * Or do we just leave that be?
         * If we do, then warp zones can get a bit strange, but as long as that's communicated
         * to the player that might be ok?
         */

        /*
        foreach ($game->worlds as $world) {
        if ($world->hasLevel('1-2') && $world->num != 0) {
        $this->log->writeVerbose("Sanity check fail: 1-2 is not in world 1!\n");
        return false;
        }
        }

        // If we pass the previous test, now check 4-2 - it has to not be in world 1
        foreach ($game->worlds as $world) {
        if ($world->hasLevel('4-2') && $world->num == 0) {
        $this->log->writeVerbose("Sanity check fail: 4-2 is in world 1!\n");
        return false;
        }
        }

        // 4-2 also should not be in world 8
        foreach ($game->worlds as $world) {
        if ($world->hasLevel('4-2') && $world->num == 7) {
        $this->log->writeVerbose("Sanity check fail: 4-2 is in world 8!\n");
        return false;
        }
        }
         */

        // Underground Bonus Area shuffle happens here
        // BUT WHY?
        // Because it needs to be repeated until success.
        // But it's kinda stupid to have it in this function, right?
        if ($this->options['shuffleUndergroundBonus'] == 'true') {
            $this->fixPipes($game);
            if (!$this->shuffleUndergroundBonusAreaDestinations($game)) {
                $this->log->write("shuffle underground bonus area FAILED\n");
                return false;
            }
        }

        return true;
    }

    public function randomizeBowserAbilities(&$game)
    {
        $this->log->write("Randomizing Bowser's abilities...\n");

        $new_hammer_world = mt_rand(0, 6);
        $new_fire_world   = mt_rand($new_hammer_world, 7);
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

    /*
     * Disable warp pipes! Makes the pipes not-enterable, players should use at own risk.
     */
    public function disableWarpPipes(&$game)
    {
        $this->log->write("Disabling Warp Zones!\n");

        $game->addData(0x2cdc, pack('C*', 0x72));
        $game->addData(0x2cde, pack('C*', 0x72));
        $game->addData(0x2ce0, pack('C*', 0x72));
        $game->addData(0x2d81, pack('C*', 0x72));
        $game->addData(0x2a03, pack('C*', 0x72));
        $game->addData(0x2a05, pack('C*', 0x72));
        $game->addData(0x2a07, pack('C*', 0x72));
        $this->removeWarpZoneLabels($game);

        $warp_text_variations = [
            "WELCOME TO NOPE ZONE!",
            "   WELCOME TO HELL   ",
            "WELCOME TO SOFT LOCK!",
            " WELCOME TO TIMEOUT! ",
            "OOOOOOOOOOOOOOOOOOPS!",
            "    OH        NO     ",
            "       SORRY!        ",
            "NO WARP SOUP FOR YOU!",
            "NO WARP ZONE FOR YOU!",
            "   NO WARP FOR YOU!  ",
            "     YOU DINGUS!     ",
            "     YOU DONGLE!     ",
            "     BREAK ROOM!     ",
            "TAKE A DEEP BREATH...",
            "    OUT OF ORDER     ",
            " UNDER CONSTRUCTION  ",
            "   PLUMBER NEEDED    ",
            "ERROR- WARP NOT FOUND",
            "     FEELSBADMAN     ",
        ];
        $text = $warp_text_variations[mt_rand(0, count($warp_text_variations) - 1)];
        // $text = $warp_text_variations[16];
        $this->setText($game, "Warp", $text);
    }

    public function writeNewWarpZoneCode(&$game)
    {
        // NEW ROUTINE:
        // ldx #$04        A2 04
        // lda $e9         A5 E9
        // cmp #$d8        C9 D8
        // beq WarpNum     F0 07
        // inx             E8
        // cmp #$05        C9 05
        // beq WarpNum     F0 02
        // inx             E8
        // nop             EA
        $new_code = [
            0xA2, 0x04,
            0xA5, 0xE9,
            0xC9, 0xD8,
            0xF0, 0x07,
            0xE8,
            0xC9, 0x05,
            0xF0, 0x02,
            0xE8,
            0xEA,
        ];

        $offset = 0x1702;

        for ($i = 0; $i < count($new_code); $i++) {
            $game->addData($offset + $i, pack('C*', $new_code[$i]));
        }
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
            $destinations          = [2, 3, 4, 5, 6, 7, 8]; // 0x24 is "blank"
            $shuffled_destinations = mt_shuffle($destinations);
            $index                 = 0;
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
             * The below comment is obsolete now that we have the new warp zone code!!
             *
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
                            $min_world = 1;
                            if (($world->num + 2) > 8) {
                                $min_world = 8;
                            } else {
                                $min_world = $world->num + 2;
                            }
                            $new_warp = mt_rand($min_world, 8);
                            $game->addData($offset + $i, pack('C*', $new_warp));
                            $this->log->write("Warp pipe in 1-2 (world $world->num) randomized to $new_warp\n");
                        }
                    }
                    if ($level->name == '4-2') {
                        // area accessed by beanstalk
                        for ($i = 8; $i < 11; $i++) {
                            $min_world = 1;
                            if (($world->num + 2) > 8) {
                                $min_world = 8;
                            } else {
                                $min_world = $world->num + 2;
                            }
                            $new_warp = mt_rand($min_world, 8);
                            $game->addData($offset + $i, pack('C*', $new_warp));
                            $this->log->write("Warp pipe in 4-2 (beanstalk area) (world $world->num) randomized to $new_warp\n");
                        }
                        // area at end of level (only one pipe there)
                        $min_world = 1;
                        if (($world->num + 2) > 8) {
                            $min_world = 8;
                        } else {
                            $min_world = $world->num + 2;
                        }
                        $new_warp = mt_rand($min_world, 8);
                        $game->addData($offset + 5, pack('C*', $new_warp));
                        $this->log->write("Warp pipe in 4-2 (end of level) (world $world->num) randomized to $new_warp\n");
                    }
                }
            }
        } else if ($this->options['warpZones'] == 'allbad') {
            foreach ($game->worlds as $world) {
                foreach ($world->levels as $level) {
                    if ($level->name == '1-2') {
                        for ($i = 0; $i < 3; $i++) {
                            $new_warp = mt_rand(1, $world->num + 1);
                            $game->addData($offset + $i, pack('C*', $new_warp));
                            $this->log->write("Warp pipe in 1-2 (world $world->num) randomized to $new_warp\n");
                        }
                    }
                    if ($level->name == '4-2') {
                        // area accessed by beanstalk
                        for ($i = 8; $i < 11; $i++) {
                            $new_warp = mt_rand(1, $world->num + 1);
                            $game->addData($offset + $i, pack('C*', $new_warp));
                            $this->log->write("Warp pipe in 4-2 (beanstalk area) (world $world->num) randomized to $new_warp\n");
                        }
                        // area at end of level (only one pipe there)
                        $new_warp = mt_rand(1, $world->num + 1);
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
                        $min_world    = (($world->num + 2) > 8 ? 8 : ($world->num + 2));
                        $good_warp[0] = mt_rand($min_world, 8);
                        $good_warp[1] = mt_rand($min_world, 8);
                        $bad_warp     = mt_rand(1, $world->num + 1);
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
                        $min_world    = (($world->num + 2) > 8 ? 8 : ($world->num + 2));
                        $good_warp[0] = mt_rand($min_world, 8);
                        $good_warp[1] = mt_rand($min_world, 8);
                        $bad_warp     = mt_rand(1, $world->num + 1);
                        $shuffled     = mt_shuffle([$good_warp[0], $good_warp[1], $bad_warp]);

                        $offset = WarpZonesOffset + 8;
                        for ($i = 0; $i < 3; $i++) {
                            $game->addData($offset + $i, pack('C*', $shuffled[$i]));
                            $this->log->write("Warp pipe in 4-2 (beanstalk area) (world $world->num) randomized to $shuffled[$i]\n");
                        }

                        // warp zone at end of 4-2 will be 50/50 good or bad
                        $chance = mt_rand(1, 100);
                        if ($chance <= 50) {
                            $min_world = (($world->num + 2) > 8 ? 8 : ($world->num + 2));
                            $new_warp  = mt_rand($min_world, 8);
                        } else {
                            $new_warp = mt_rand(1, $world->num + 1);
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
        $digits        = [1, 2, 3, 4, 5, 6, 7, 8, 9];
        $random_digits = mt_shuffle($digits);

        // use indexes 1, 3, 6 just as a nod to the original game :)
        $game->addData(FireworksOffset1, pack('C*', $random_digits[1]));
        $game->addData(FireworksOffset2, pack('C*', $random_digits[3]));
        $game->addData(FireworksOffset3, pack('C*', $random_digits[6]));
        $this->log->write("Fireworks will appear when last digit of timer is " . $random_digits[1] . ", " . $random_digits[3] . " or " . $random_digits[6] . "\n");
    }

    public function randomizeBackground(&$game)
    {
        $this->log->write("Randomizing background and scenery...\n");
        foreach ($game->worlds as $world) {
            foreach ($world->levels as $level) {
                if ($level->level_data_offset != 0) {
                    $this->log->write("$level->name\n");
                    $headerByte1 = $this->rom->read($level->level_data_offset);
                    $headerByte2 = $this->rom->read($level->level_data_offset + 1);
                    $level->setHeaderBytes($headerByte1, $headerByte2);
                    $this->log->writeVerbose(sprintf("Read header bytes: %02x %02x\n", $headerByte1, $headerByte2));
                    $this->log->writeVerbose("Old background: " . $level->getBackgroundDescription() . "\n");
                    $this->log->writeVerbose("Old scenery:    " . $level->getSceneryDescription() . "\n");
                    $this->log->writeVerbose("Old compliment: " . $level->getComplimentDescription() . "\n");

                    $newScenery    = mt_rand(0, 3);
                    $newCompliment = mt_rand(0, 2);
                    $newBackground = mt_rand(0, 7);
                    $level->setScenery($newScenery);
                    $level->setCompliment($newCompliment);
                    $level->setBackground($newBackground);

                    $newBytes = $level->getHeaderBytes();
                    $this->log->writeVerbose(sprintf("New header bytes: %02x %02x\n", $newBytes[0], $newBytes[1]));
                    $this->log->writeVerbose("New background: " . $level->getBackgroundDescription() . "\n");
                    $this->log->writeVerbose("New scenery:    " . $level->getSceneryDescription() . "\n");
                    $this->log->writeVerbose("New compliment: " . $level->getComplimentDescription() . "\n");
                }
            }
        }
    }

    // TODO: rewrite! (???)
    public function fixPipes(Game &$game)
    {
        $levels = ['1-1', '1-2', '2-1', '2-2', '3-1', '4-1', '4-2', '5-1', '5-2', '6-2', '7-1', '7-2', '8-1', '8-2'];
        $this->log->write("Fixing Pipes...\n");
        foreach ($game->worlds as $world) {
            foreach ($world->levels as $level) {
                if (in_array($level->name, $levels)) {
                    if ($level->pipe_pointers) {
                        foreach ($level->pipe_pointers as list($entry, $exit)) {
                            if ($entry != null) {
                                $entry->setWorldActive($world->num);
                            }

                            if ($exit != null) {
                                $exit->setWorldActive($world->num);
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

    public function fixLoopingCastles(Game &$game)
    {
        $this->log->write("Fixing looping castles (4-4 and 7-4)...\n");
        $offset44 = 0x407b;
        $offset74 = 0x407d;
        $world44  = 0;
        $world74  = 0;

        foreach ($game->worlds as $world) {
            if ($world->hasLevel('4-4')) {
                $world44 = $world->num;
            }
            if ($world->hasLevel('7-4')) {
                $world74 = $world->num;
            }
        }

        if ($world44 == $world74) {
            $this->log->write("FATAL ERROR: 4-4 and 7-4 are in the same world?!?!?!?!?!\n");
            exit(1);
        }

        $game->addData($offset44 + 0, pack('C*', $world44));
        $game->addData($offset44 + 1, pack('C*', $world44));
        $game->addData($offset74 + 0, pack('C*', $world74));
        $game->addData($offset74 + 1, pack('C*', $world74));
        $game->addData($offset74 + 2, pack('C*', $world74));
        $game->addData($offset74 + 3, pack('C*', $world74));
        $game->addData($offset74 + 4, pack('C*', $world74));
        $game->addData($offset74 + 5, pack('C*', $world74));
    }

    public function enableOHKO(Game &$game)
    {
        $offset = 0x5941;
        // New code:
        //
        // LDX #00          ; set Mario status to small Mario (basically)
        // JMP $D958        ; jump to KillPlayer routine
        //
        // = A2 00 4C 58 D9
        $game->addData($offset + 0, pack('C*', 0xA2));
        $game->addData($offset + 1, pack('C*', 0x00));
        $game->addData($offset + 2, pack('C*', 0x4C));
        $game->addData($offset + 3, pack('C*', 0x58));
        $game->addData($offset + 4, pack('C*', 0xD9));
    }

    /*
     * Set coin tallies needed to activate bonus hidden 1-UP blocks to zero
     */
    public function zeroCoinTallies(Game &$game)
    {
        $offset = 0x32d2;
        for ($i = 0; $i < 8; $i++) {
            $game->addData($offset + $i, pack('C*', 0x00));
        }
    }

    public function alwaysGiveHidden1UPs(Game &$game)
    {
        // Overwrite code which checks if Hidden1UPFlag is set
        $offset = 0x1b11;
        $game->addData($offset + 0, pack('C*', 0xA9));
        $game->addData($offset + 1, pack('C*', 0x01)); // LDA #$01
        $game->addData($offset + 2, pack('C*', 0xEA)); // NOP
    }

    public function disableDemoActions(Game &$game)
    {
        for ($offset = 0x350; $offset < 0x364; $offset++) {
            $game->addData($offset, pack('C*', 0x80));
        }
    }

    public function shuffleMusic(Game &$game)
    {
        $bytes    = [0x01, 0x02, 0x04, 0x08];
        $offset   = 0x10f7;
        $shuffled = mt_shuffle($bytes);

        for ($i = 0; $i < 4; $i++) {
            $game->addData($offset + $i, pack('C*', $shuffled[$i]));
        }
    }

    public function setTextSeedhash(string $text, Game &$game)
    {
        $offset      = 0x9fa1; // + 0x8000;   if using smb+duckhunt rom
        $titleOffset = 0x9f9c;
        $this->log->write("Writing Seedhash on title screen...\n");

        if ($this->options['mysterySeed'] == false) {
            $game->addData($titleOffset + 0, pack('C*', $this->trans->asciitosmb('S')));
            $game->addData($titleOffset + 1, pack('C*', $this->trans->asciitosmb('M')));
            $game->addData($titleOffset + 2, pack('C*', $this->trans->asciitosmb('B')));
            $game->addData($titleOffset + 3, pack('C*', $this->trans->asciitosmb('R')));
            $game->addData($titleOffset + 4, pack('C*', $this->trans->asciitosmb(' ')));

            $game->addData($titleOffset + 5, pack('C*', $this->trans->asciitosmb('S')));
            $game->addData($titleOffset + 6, pack('C*', $this->trans->asciitosmb('E')));
            $game->addData($titleOffset + 7, pack('C*', $this->trans->asciitosmb('E')));
            $game->addData($titleOffset + 8, pack('C*', $this->trans->asciitosmb('D')));
            $game->addData($titleOffset + 9, pack('C*', $this->trans->asciitosmb('H')));
            $game->addData($titleOffset + 10, pack('C*', $this->trans->asciitosmb('A')));
            $game->addData($titleOffset + 11, pack('C*', $this->trans->asciitosmb('S')));
            $game->addData($titleOffset + 12, pack('C*', $this->trans->asciitosmb('H')));
            $game->addData($titleOffset + 13, pack('C*', $this->trans->asciitosmb(' ')));
        } else {
            $game->addData($titleOffset + 0, pack('C*', $this->trans->asciitosmb('S')));
            $game->addData($titleOffset + 1, pack('C*', $this->trans->asciitosmb('M')));
            $game->addData($titleOffset + 2, pack('C*', $this->trans->asciitosmb('B')));
            $game->addData($titleOffset + 3, pack('C*', $this->trans->asciitosmb('R')));
            $game->addData($titleOffset + 4, pack('C*', $this->trans->asciitosmb(' ')));

            $game->addData($titleOffset + 5, pack('C*', $this->trans->asciitosmb('M')));
            $game->addData($titleOffset + 6, pack('C*', $this->trans->asciitosmb('Y')));
            $game->addData($titleOffset + 7, pack('C*', $this->trans->asciitosmb('S')));
            $game->addData($titleOffset + 8, pack('C*', $this->trans->asciitosmb('T')));
            $game->addData($titleOffset + 9, pack('C*', $this->trans->asciitosmb('E')));
            $game->addData($titleOffset + 10, pack('C*', $this->trans->asciitosmb('R')));
            $game->addData($titleOffset + 11, pack('C*', $this->trans->asciitosmb('Y')));
            $game->addData($titleOffset + 12, pack('C*', $this->trans->asciitosmb('!')));
            $game->addData($titleOffset + 13, pack('C*', $this->trans->asciitosmb(' ')));
        }
        /*
         * Write the first 8 characters of the seedhash on title screen.
         * TODO: see if there's a way to draw some sprites instead!
         * DONE: there probably is, but no good sprites it seems. Text/numbers is better.
         */
        for ($i = 0; $i < 8; $i++) {
            $game->addData($offset + 9 + $i, pack('C*', $this->trans->asciitosmb($text[$i])));
        }
    }

    public function setText(&$game, $text, $newtext)
    {
        $messages = [
            // first 3 bytes = header, last = 0x00 - keep those!
            "ThankYouMario" => [0xd64, 0xd77], // THANK YOU MARIO!
            "ThankYouLuigi" => [0xd78, 0xd8b], // THANK YOU LUIGI!
            "AnotherCastle" => [0xd8c, 0xdb7], // BUT OUR PRINCESS IS IN \ ANOTHER CASTLE!
            "QuestOver"     => [0xdb8, 0xdce], // YOUR QUEST IS OVER.
            "NewQuest"      => [0xdcf, 0xded], // WE PRESENT YOU A NEW QUEST.
            "WorldSelect"   => [0xdee, 0xdfe], // PUSH BUTTON B
            "WorldSelect2"  => [0xdff, 0xe13], // TO SELECT A WORLD
            "Warp"          => [0x7d0, 0x7e7], // WELCOME TO WARP ZONE!
        ];

        $offset     = $messages[$text][0] + 3;
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

    public function makeFlags()
    {
        $this->flags = $this->flagstring->getFlagstring();
        //$this->flagstringToOptions($this->flags, $this->options);
    }

    public function makeSeedHash()
    {
        // mystery seed ?
        $hashstring     = $this->flags . strval($this->getSeed() . \App\SMBR\Randomizer::VERSION . $this->rom->getMD5()) . $this->log->getActuallySave();
        $this->seedhash = hash("crc32b", $hashstring);
    }

    public function getSeedHash()
    {
        return $this->seedhash;
    }

    public function setSeed(int $rng_seed = null)
    {
        $rng_seed       = $rng_seed ?: random_int(1, 9999999999999); // cryptographic pRNG for seeding
        $this->rng_seed = $rng_seed % 10000000000000;
        mt_srand($this->rng_seed);
    }

    // Here we go!
    public function makeSeed()
    {
        include "TextVariations.php";

        $game       = new Game($this->options);
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

        // Shuffle Levels
        // This part can be optimized / written less strangely. Callback functions seems like a good idea.
        // Note: Sanity checking takes care of underground bonus area shuffling!
        // TODO: maybe structure that differently?
        if ($this->options['shuffleLevels'] == "all") {
            if ($this->options['pipeTransitions'] == "keep") {
                $game->num_levels = 35;
            } else {
                $game->num_levels = 32;
            }
            if ($this->options['normalWorldLength'] == "true") {
                // normal world length
                $this->log->write("Shuffling all levels (normal world length)...\n");
                $this->shuffleLevelsWithNormalWorldLength($game);
                while (!$this->sanityCheckWorldLayout($game)) {
                    $game->resetWorlds();
                    $this->shuffleLevelsWithNormalWorldLength($game);
                }
            } else {
                // any world length
                $this->log->write("Shuffling all levels (varying world length)...\n");
                $this->shuffleAllLevels($game);
                while (!$this->sanityCheckWorldLayout($game)) {
                    $game->resetWorlds();
                    $this->shuffleAllLevels($game);
                }
            }

            // Make it possible to always get the hidden 1-UPs (i.e. ignore coin tallies)
            // TODO: make this a separate option?
            $this->alwaysGiveHidden1UPs($game);

        } else if ($this->options['shuffleLevels'] == "worlds") {
            $this->log->write('Shuffling world order...');
            $game->num_levels = 32;
            $this->shuffleWorldOrder($game);
            while (!$this->sanityCheckWorldLayout($game)) {
                $game->resetWorlds();
                $this->shuffleWorldOrder($game);
            }
            $game->setVanillaWorldData();
        } else if ($this->options['shuffleLevels'] == "none") {
            $game->setVanillaWorldData();
            $game->num_levels = 35;
            while (!$this->sanityCheckWorldLayout($game)) {
                $this->log->write("Vanilla World Layout sanity check failed?!! Something is very wrong - that shouldn't happen!\n");
                exit(1);
            }
        } else {
            print("Unrecognized option " . $this->options['shuffleLevels'] . " for Shuffle Levels! Exiting...");
            exit(1);
        }

        // Fix pipes
        if ($this->options['shuffleUndergroundBonus'] == 'false') {
            $this->fixPipes($game);
        }

        // Randomize Enemies
        if ($this->options['enemies'] == "randomizeChaos") {
            $this->randomizeEnemiesChaos($game);
        } else if ($this->options['enemies'] == "randomizeControlled") {
            $this->randomizeEnemiesControlled($game);
        } else if ($this->options['enemies'] == "randomizeNone") {
            $this->log->write("No randomization of enemies!\n");
        }

        // Randomize Blocks
        if ($this->options['blocks'] == "randomizeAll") {
            $this->randomizeBlocks($game, $item_pools->all_items, $item_pools->all_items);
            $this->alwaysGiveHidden1UPs($game);
        } else if ($this->options['blocks'] == "randomizePowerups") {
            $this->randomizeBlocks($game, $item_pools->powerups, $item_pools->powerups);
        } else if ($this->options['blocks'] == "randomizeGrouped") {
            $this->randomizeBlocks($game, $item_pools->all_question_blocks, $item_pools->all_question_blocks);
            $this->randomizeBlocks($game, $item_pools->all_hidden_blocks, $item_pools->all_hidden_blocks);
            $this->randomizeBlocks($game, $item_pools->all_brick_blocks, $item_pools->all_brick_blocks);
            $this->alwaysGiveHidden1UPs($game);
        } else if ($this->options['blocks'] == "randomizeBricks") {
            $this->randomizeBlocks($game, $item_pools->all_brick_blocks, $item_pools->all_brick_blocks);
        } else if ($this->options['blocks'] == "randomizeBricksQuestion") {
            $this->randomizeBlocks($game, $item_pools->all_brick_blocks, $item_pools->all_brick_blocks);
            $this->randomizeBlocks($game, $item_pools->all_question_blocks, $item_pools->all_question_blocks);
        } else if ($this->options['blocks'] == "randomizeCoins") {
            $this->randomizeBlocks($game, $item_pools->all_items, $item_pools->all_coins);
        } else if ($this->options['blocks'] == "randomizeNone") {
            $this->log->write("No randomization of blocks!\n");
        }

        // Randomize Blocks in Underground Bonus Areas
        if ($this->options['randomizeUndergroundBricks'] == 'true') {
            $this->randomizeBlocksInUndergroundBonus($game, $item_pools->all_brick_blocks, $item_pools->all_brick_blocks);
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
            if ($this->options['warpZones'] == "disable") {
                $this->disableWarpPipes($game);
            } else {
                $this->randomizeWarpZones($game);
            }
        }

        // Remove warp zone destination text if selected
        if ($this->options['hiddenWarpDestinations'] == "true") {
            $this->removeWarpZoneLabels($game);
        }

        // Randomize fireworks
        if ($this->options['fireworks'] == "true") {
            $this->randomizeFireworks($game);
        }

        // Randomize background/scenery
        if ($this->options['randomizeBackground'] == "true") {
            $this->randomizeBackground($game);
        }

        // Randomize where secondary hard mode starts
        // if ($this->options['hardMode'] == 'random') {
        //     $this->randomizeSecondaryHardModeStart($game);
        // } else if ($this->options['hardMode'] == 'always') {
        //     $this->setAlwaysSecondaryHardMode($game);
        // }

        // Randomize Fire Bar Spin Speed
        if ($this->options['randomizeSpinSpeed'] == 'true') {
            $this->randomizeSpinSpeed($game);
        }

        // Shuffle Fire Bar Spin Directions
        if ($this->options['shuffleSpinDirections'] == 'true') {
            $this->shuffleSpinDirections($game);
        }

        // Shuffle music
        if ($this->options['shuffleMusic'] == 'true') {
            $this->shuffleMusic($game);
        }

        if ($this->options['ohko'] == 'true') {
            $this->enableOHKO($game);
        }

        // Fix Midway Points
        $this->fixMidwayPoints($game);

        // Fix Looping Castles
        $this->fixLoopingCastles($game);

        // Zero coin tallies
        // TODO: THIS DOESN'T SEEM TO WORK!!!!!
        // $this->zeroCoinTallies($game);

        // Write New Warp Zone Code :D
        $this->writeNewWarpZoneCode($game);

        // Disable demo actions
        $this->disableDemoActions($game);

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
        $pull_key  = mt_rand(0, count($array) - 1);
        $new_array = array_merge($new_array, array_splice($array, $pull_key, 1));
    }

    return $new_array;
}
