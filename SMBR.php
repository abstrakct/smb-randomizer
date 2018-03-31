<?php namespace SMBR;

use SMBR\Game;

/*
 *
 * Sources used:
 * https://github.com/justinmichaud/rust-nes-emulator/
 * http://1st.geocities.jp/bysonshome/smb1/
 */

/*
 *
 * TODO: Randomize where pipes lead to?
 * TODO: Randomize Bowser's abilities? like hammers etc.?
 * TODO: Randomize power ups
 *
 * BUGS:
 * TODO: warp zone pipes have strange behavior (pipes with no number take you to world -1)
 * TODO: when mario dies and start in the middle of a level it is sometimes in a very wrong spot - potential soft lock / nothing you can do but game over
 * ----> see disasm lines 4351, 7787 and on
 * ----> also level data pdf!!!
 *
 * offset: 0x11cd <- this is where we write the new midway points.
 *
 *
 * TODO: disappearing trampolines
 * TODO: check that table at 0x1cc4 is correct in all situations. (I'm pretty sure it is, but check it.)
 * TODO: randomize y pos of (some) enemies? as option? to prevent stuck enemies
 *
 *
 * NOTES
 * - Once a piranha plant was hiding behind a tree in 8-1
 *
 */

$smbr_version = "0.1";


require_once "Enemy.php";
require_once "Game.php";
require_once "Rom.php";
require_once "Levels.php";
require_once "Randomizer.php";
require_once "Logger.php";
require_once "Dump.php";
require_once "Colorscheme.php";
require_once "Text.php";


$options['Mario Color Scheme'] = "random";
$options['Luigi Color Scheme'] = "random";
$options['Fire Color Scheme']  = "random";
/*
 * Pipe Transitions (like between 1-1 and 1-2) can be:
 * remove  - just remove them entirely
 * keep    - add pipe transitions before levels that have them in vanilla - i.e. you'd get a pipe transition before playing vanilla 1-2
 *
 * third possibility: shuffle pipe transitions in with normal levels, so that they can appear anywhere (but only as many times as in vanilla)
 */
$options['Pipe Transitions'] = "remove";
/*
 * Shuffle Levels can be
 * true  - shuffle levels
 * false - don't shuffle levels
 */
$options['Shuffle Levels'] = "true";
/*
 * Normal World Length can be
 * true  - make sure each world has 4 levels, last level of each world is a castle.
 * false - castles can appear anywhere and will take you to the next world when beaten. Worlds can be 1 - 25 levels long.....
 *         total number of levels will still be 32, like in vanilla.
 *
 * 8-4 will always be last.
 */
$options['Normal World Length'] = "true";
/*
 * Shuffle Enemies can be
 * true  - shuffle enemies
 * false - don't shuffle enemies
 *
 * Future features: add options to choose between "full" shuffle (any enemy can appear anywhere an enemy normally is)
 * and more sensible shuffling (divide enemies into different pools of enemies to shuffle).
 * Some restrictions has to be applied anyway, since full randomization can break the game in certain cases.
 * Also, maybe make some enemies more rare than others.. and don't shuffle some, like Bowser. Or add option to do so...
 * TODO: LIFTS ARE ENEMIES!!! Don't shuffle in a game-breaking way!!!
 */
$options['Shuffle Enemies'] = "true";




if ($argc <= 1) {
    print "Please provide ROM filename.\n";
    exit(1);
}

if ($argv[1] == "-d") {
    $filename = $argv[2];
    $rom = new Rom($filename);
    dumpRomInfoActualOrder($rom);
    exit(0);
}

$randomseed = true;
if ($argc > 2) {
    $chosenseed = $argv[2];
    $randomseed = false;
}

$vanilla = new Game();
$vanilla->setVanilla();
//print_r($vanilla);
//print(count($vanilla->worlds[1]->levels));




$filename = $argv[1];
$rom = new Rom($filename);
$checksum = $rom->getMD5();
$ok = $rom->checkMD5();

print("\n\nSMB RANDOMIZER\n\nROM filename: $filename\n");
print("MD5 checksum: $checksum");
if($ok) {
    print(" [OK]\n");
} else {
    print(" [FAILED!]\n");
    print("trying anyway, results may vary....\n");
    //TODO: Add checks to see if ROM is usable (check data in various offsets).
    //exit(1);
}

//print("Reading ROM data...");
//$romdata = $rom->read(0, Rom::SIZE);
//print("OK\n");

print("\n");

if($randomseed) {
    // pick a random number to be used as the seed
    $rando = new Randomizer(null, $options, $rom);
} else {
    // user has provided a wanted seed, so use that one.
    $rando = new Randomizer($chosenseed, $options, $rom);
}

$rando->setSeed($rando->getSeed());
$rando->makeFlags();
$outfilename = "roms/smb-rando-" . $rando->getSeed() . "-" . strtoupper($rando->getFlags()) . ".nes";
$logfilename = "logs/smb-rando-" . $rando->getSeed() . "-" . strtoupper($rando->getFlags()) . ".log";
$log = new Logger($logfilename);
$rom->setLogger($log);

// Print out the selected options and relevant information
$rando->printOptions();

// Make the seed a.k.a. this performs the actual randomization!
$randomized_game = $rando->makeSeed();

$gamejson = json_encode($randomized_game, JSON_PRETTY_PRINT);
print $gamejson;
//print_r($randomized_game);


$rom->writeGame($randomized_game);

$rom->save($outfilename);

$log->close();

print("\nFinished!\nFilename: $outfilename\n");

