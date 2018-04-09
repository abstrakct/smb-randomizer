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
 *       - we could change the code which selects bowser's abilities based on which world you're in
 *
 * BUGS:
 * TODO: warp zone pipes have strange behavior (pipes with no number take you to world -1)
 *
 *
 * TODO: disappearing trampolines
 * TODO: check that table at 0x1cc4 is correct in all situations. (I'm pretty sure it is, but check it.)
 * TODO: randomize y pos of (some) enemies? as option? to prevent stuck enemies
 * TODO: option to randomize music?
 *
 * TODO:
 * for power up shuffling - options:
 * DONE - only power ups are random (flower, star, 1 up)
 * DONE - add coins to that pool
 * - shuffle all coins/powerups in vanilla in one big pool, so that you in total get the same number of coins/powerups, but don't know where they are
 *
 * NOTES
 * - Once a piranha plant was hiding behind a tree in 8-1
 *
 */

$smbr_version = "0.6.1";


require_once "Enemy.php";
require_once "Game.php";
require_once "Rom.php";
require_once "Level.php";
require_once "Randomizer.php";
require_once "Logger.php";
require_once "Dump.php";
require_once "Colorscheme.php";
require_once "Text.php";
require_once "Item.php";

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
$options['Normal World Length'] = "false";

/*
 * Shuffle Enemies can be
 * full  - shuffle enemies (within reason)
 * pools - shuffle enemies within pools of related/similar enemies
 * false - don't shuffle enemies
 *
 * Future features: add options to choose between "full" shuffle (any enemy can appear anywhere an enemy normally is)
 * and more sensible shuffling (divide enemies into different pools of enemies to shuffle).
 * Some restrictions has to be applied anyway, since full randomization can break the game in certain cases.
 * Also, maybe make some enemies more rare than others?
 */
$options['Shuffle Enemies'] = "full";

/*
 * Shuffle Blocks can be
 * all        - randomize all blocks that normally contain a coin, powerup, star or 1-UP.
 *              Exception: rows of e.g. question blocks are special objects, those are not randomized, and probably can't be anyway.
 * powerups   - only randomize blocks containing a powerup (mushroom/flower, star or 1-UP), leave coins out of the equation.
 * grouped    - randomize blocks in sensible groups, e.g. all hidden blocks are shuffled amongst themselves, all bricks with items
 *              are shuffled amongst themselves, etc. In other words: where you would normally expect a brick containing a mushroom,
 *              that brick will now contain a random item (powerup, star, 1up, coin).
 * coins      - all powerups are replaced with coins! no more mushrooms, 1ups or stars!
 * none       - no randomization of blocks
 */
$options['Shuffle Blocks'] = "all";

$log = null;

function printVersion() {
    global $smbr_version;

    return "version " . $smbr_version;
}

function smbrMain($filename, $seed = null, $webmode = false) {
    global $options, $log;
    //$vanilla = new Game();
    //$vanilla->setVanilla();
    //print_r($vanilla);
    //print(count($vanilla->worlds[1]->levels));
    
    $rom = new Rom($filename);
    $checksum = $rom->getMD5();
    $ok = $rom->checkMD5();
    
    if ($webmode) {
        print("<br><br>SMB RANDOMIZER " . printVersion() . "<br><br>ROM filename: $filename<br>");
        print("MD5 checksum: $checksum");
        if ($ok) {
            print(" <b>[OK]</b><br>");
        } else {
            print(" <b>[FAILED!]</b><br>");
            print("Trying to use this ROM anyway, <b>not guaranteed to work,</b> results may vary...<br>");
            //TODO: Add checks to see if ROM is usable (check data in various offsets).
        }
    } else {
        print("\n\nSMB RANDOMIZER " . printVersion() . "\n\nROM filename: $filename\n");
        print("MD5 checksum: $checksum");
        if ($ok) {
            print(" [OK]\n");
        } else {
            print(" [FAILED!]\n");
            print("Trying to use this ROM anyway, not guaranteed to work, results may vary...\n");
            //TODO: Add checks to see if ROM is usable (check data in various offsets).
        }
    }
    
    print("\n");
    
    // if seed == null a random seed will be chosen, else it will use the user's chosen seed.
    $rando  = new Randomizer($seed, $options, $rom);
    
    $rando->setSeed($rando->getSeed());
    $rando->makeFlags();

    if ($webmode) {
        $dir = "webout/" . $rando->getSeed() . "-" . strtoupper($rando->getFlags());
        if (!file_exists($dir))
            mkdir($dir, 0744);
        $outfilename = $dir . "/smb-rando-" . $rando->getSeed() . "-" . strtoupper($rando->getFlags()) . ".nes";
        $logfilename = $dir . "/smb-rando-" . $rando->getSeed() . "-" . strtoupper($rando->getFlags()) . ".log";
    } else {
        $outfilename = "roms/smb-rando-" . $rando->getSeed() . "-" . strtoupper($rando->getFlags()) . ".nes";
        $logfilename = "logs/smb-rando-" . $rando->getSeed() . "-" . strtoupper($rando->getFlags()) . ".log";
    }

    $log = new Logger($logfilename);
    $rom->setLogger($log);
    
    // Print out the selected options and relevant information
    $rando->printOptions();
    
    // Make the seed a.k.a. this performs the actual randomization!
    $randomized_game = $rando->makeSeed();
    
    $game_json = json_encode($randomized_game, JSON_PRETTY_PRINT);
    //print $gamejson;
    //print_r($randomized_game);
    
    
    $rom->writeGame($randomized_game);
    
    $rom->save($outfilename);
    
    $log->write("\nJSON:\n\n");
    $log->write($game_json);

    $log->close();
    
    if ($options["webmode"]) {
        print('<br><br><b>Finished!</b><br><a href="' . $outfilename. '">Click here to download randomized ROM!</a>');
        print('<br><a href="' . $logfilename. '">Click here to view the log (contains spoilers!)</a>');
    } else {
        print("\nFinished!\nFilename: $outfilename\n");
    }
}

if (php_sapi_name() == "cli") {
    global $options;
    $options["webmode"] = false;

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

    $filename = $argv[1];    
    if ($argc > 2) {
        $chosenseed = $argv[2];
        smbrMain($filename, $chosenseed);
    } else {
        smbrMain($filename);
    }
} else {
    global $options;
    ini_set('display_errors',1); 
    error_reporting(E_ALL);

    echo "<html><body>";
    echo "Starting the Randomizationing...<br>";

    if ($_POST["shufflelevels"] == "yes")
        $options['Shuffle Levels'] = "true";
    else if ($_POST["shufflelevels"] == "no")
        $options['Shuffle Levels'] = "false";

    if ($_POST["normalworldlength"] == "yes")
        $options['Normal World Length'] = "true";
    else if ($_POST["normalworldlength"] == "no")
        $options['Normal World Length'] = "false";

    // this part could probably be done simpler, but at the same time I want
    // to make sure the input is exactly what it should be.
    if ($_POST["pipetransitions"] == "keep")
        $options['Pipe Transitions'] = "keep";
    else if($_POST["pipetransitions"] == "remove")
        $options['Pipe Transitions'] = "remove";

    if ($_POST["shuffleenemies"] == "full")
        $options['Shuffle Enemies'] = "full";
    else if($_POST["shuffleenemies"] == "pools")
        $options['Shuffle Enemies'] = "pools";
    else if($_POST["shuffleenemies"] == "none")
        $options['Shuffle Enemies'] = "none";

    if ($_POST["shuffleblocks"] == "all")
        $options['Shuffle Blocks'] = "all";
    else if ($_POST["shuffleblocks"] == "powerups")
        $options['Shuffle Blocks'] = "powerups";
    else if ($_POST["shuffleblocks"] == "grouped")
        $options['Shuffle Blocks'] = "grouped";
    else if ($_POST["shuffleblocks"] == "coins")
        $options['Shuffle Blocks'] = "coins";
    else if ($_POST["shuffleblocks"] == "none")
        $options['Shuffle Blocks'] = "none";

    $options["Mario Color Scheme"] = $_POST["mariocolor"];
    $options["Luigi Color Scheme"] = $_POST["luigicolor"];
    $options["Fire Color Scheme"]  = $_POST["firecolor"];

    $filename = "Super Mario Bros. (JU) [!].nes";
    if ($_POST["seed"])
        $seed = $_POST["seed"];
    else
        $seed = null;

    $options["webmode"] = true;
    smbrMain($filename, $seed, true);

    echo "</body></html>";
}
