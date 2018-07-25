<?php namespace SMBR;

use SMBR\Game;

/*
 *
 * Sources used:
 * https://github.com/justinmichaud/rust-nes-emulator/
 * http://1st.geocities.jp/bysonshome/smb1/
 * and more
 */

/*
 *
 * BUG: SOMETHING IS WRONG WITH THE PIPE POINTERS!!!!!!!!!????????????
 *
 * TODO: Check this: DONE more or less: Option to only randomize world order, but leave levels normal - e.g. the first world can be vanilla world 3,
 *      containing vanilla 3-1, 3-2, 3-3, 3-4, second world can be vanilla world 5, with levels 5-1 - 5-4, etc.
 *
 * TODO: Randomize where pipes lead to?
 * TODO: Randomize Fake Bowser identities!? possible? yes, SEE LINE 11121 IN DISASM!!
 * TODO: Option to randomize music? (line 2790 in disasm)
 * TODO: Randomize when fireworks appear? (line 10469 of disasm)
 * TODO: shuffle all coins/powerups in vanilla in one big pool, so that you in total get the same number of coins/powerups, but don't know where they are
 * TODO: fix warp zones! warp zone pipes have strange behavior (pipes with no number take you to world -1)
 *       not sure if anything can be done about this, except maybe keep levels with warp zones in their vanilla world?!
 *       see line 1682 in disasm
 * TODO: disappearing trampolines
 * TODO: disappearing powerup blocks sometimes - might be related to stuff around line 5860 in disasm
 * TODO: randomize y pos of (some) enemies? as option? to prevent stuck enemies
 * TODO: randomize x pos of Toad/substitute Toad? To prevent Mario getting hit by a hammer bro or something after defeating Bowser.
 * TODO: option to only randomize clothes for mario/luigi, for more reasonable colors, hopefully.
 * TODO: option to not randomize texts
 * TODO: keep randomized texts independent of game seed? or make it an option?
 * TODO: option to keep castles in vanilla order?
 * TODO: randomize enemies in pools better! from-/to-pools
 * TODO: check that all options are set to a valid value!
 * TODO: disasm line  1084: change demo action data in order to not spoil first level??  offset 0x350 in rom, length 20 bytes
 * TODO: disasm line  7338: maybe a better way to shuffle powerups? for mode that changes all powerups of one type to another.
 * TODO: disasm line  7787: respawn points - I think. look at that
 * TODO: disasm line  8370: fire bar spin speed / direction -- can be randomized!?
 * TODO: disasm line  8405: cheep cheep data -- can be randomized!?
 * TODO: disasm line  8698: enemy frenzy thing that checks for world 2 - should be changed??
 * TODO: disasm line 11440: it's possible/easy(?) to change which enemies can be stomped and not!
 * TODO: disasm line 13475: it's possible/easy(?) to change the appearance of a powerup!!!    NOPE DOESN'T WORK (sprites get weird)
 *
 * for power up shuffling - options:
 * DONE - only power ups are random (flower, star, 1 up)
 * DONE - add coins to that pool
 * DONE: Randomize Bowser's abilities? like hammers etc.?
 *       - we could change the code which selects bowser's abilities based on which world you're in
 * DONE: Randomize Bowser's hitpoints? For when you kill him with fire.
 *
 * NOTES
 * - Sometimes enemies "hide" behind scenery.
 *
 * 
 *
 *
 * HASH = 11 0A 1C 11
 * offset = 0x9fd6  (overwrites "2 PLAYER GAME")
 */



require_once "Version.php";
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
 * Pipe Transitions (like the one between 1-1 and 1-2) can be:
 * remove  - just remove them entirely
 * keep    - add pipe transitions before levels that have them in vanilla - i.e. you'd get a pipe transition before playing vanilla 1-2
 *
 * third possibility: shuffle pipe transitions in with normal levels, so that they can appear anywhere (but only as many times as in vanilla)
 */
$options['Pipe Transitions'] = "remove";

/*
 * Shuffle Levels can be
 * all    - shuffle all levels
 * worlds - shuffle only the world order ('Normal World Length' option will be ignored in this case). World 8 will always be last, though.
 *          the 'Pipe Transitions' option works as expected.
 * false  - don't shuffle levels 
 */
$options['Shuffle Levels'] = "all";

/*
 * Normal World Length can be
 * true  - make sure each world has 4 levels, last level of each world is a castle.
 * false - castles can appear anywhere and will take you to the next world when beaten. Worlds can theoretically be 1 - 25 levels long.....
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
$options['Shuffle Enemies'] = "pools";

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

/*
 * Bowser Abilities can be
 * true  - randomly change which world Bowser starts throwing hammers and breathing fire
 * false - don't change Bowser's abilities.
 */
$options['Bowser Abilities'] = "true";

/*
 * Bowser Hitpoints lets Bowser have a random number of hitpoints (i.e. how many times you have to shoot him with fireballs to take him down).
 * Options:
 * normal - Bowser has 5 hitpoints, as in vanilla
 * easy   - Bowser has a random amount of hitpoints between 1-5
 * medium - Bowser has a random amount of hitpoints between 5-10
 * hard   - Bowser has a random amount of hitpoints between 10-20
 */
$options['Bowser Hitpoints'] = "hard";

$log = null;

$known_good_hashes = [ "811b027eaf99c2def7b933c5208636de", "673913a23cd612daf5ad32d4085e0760" ];
$hash_to_filename =  [
    "811b027eaf99c2def7b933c5208636de" => "uploaded_roms/Super Mario Bros. (JU) [!].nes",
    "673913a23cd612daf5ad32d4085e0760" => "uploaded_roms/Super Mario Bros. (E).nes"
];
const ROMSIZE = 40976;

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

    // Start the logger
    $log = new Logger($logfilename);
    $rom->setLogger($log);
    
    // Print out the selected options and relevant information
    $rando->printOptions();
    
    // Make the seed a.k.a. this performs the actual randomization!
    $randomized_game = $rando->makeSeed();
    
    // Write all changes (to temporary file)
    $rom->writeGame($randomized_game);
    
    // Save the new ROM file
    $rom->save($outfilename);
    
    // write JSON formatted data to logfile
    $game_json = json_encode($randomized_game, JSON_PRETTY_PRINT);
    $log->write("\nJSON:\n\n");
    $log->write($game_json);
    $log->write("\n\n");

    // write "pretty" world layout to logfile
    //$log->write($randomized_game->prettyprint());

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

    // First, deal with the ROM file chosen to be uploaded
    // TODO: MD5 check 
    $target_dir = "uploaded_roms/";
    $target_file = $target_dir . basename($_FILES["fileToUpload"]["name"]);
    $uploadOk = 1;
    $romFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));

    $uploadedFileMD5 = hash_file('md5', $_FILES["fileToUpload"]["tmp_name"]);
    $uploadedFileMD5OK = 0;
    if (in_array($uploadedFileMD5, $known_good_hashes)) {
        $target_file = $hash_to_filename[$uploadedFileMD5];
        $uploadedFileMD5OK = 1;
        echo "MD5 checksum of uploaded file matches known working ROM.<br>";
    } else {
        echo "MD5 checksum does NOT match any known working ROM. Trying to use it anyway - but you are on your own now! NO GUARANTEES that this will produce a playable ROM!<br>";
    }

    //if (file_exists($target_file)) {
    //    $uploadOk = 1;
    //}
    
    if ($_FILES["fileToUpload"]["size"] != ROMSIZE) {
        echo "Sorry, wrong file size.<br>";
        $uploadOk = 0;
    }
    
    if ($romFileType != "nes") {
        echo "Sorry, only NES files are allowed.<br>";
        $uploadOk = 0;
    }
    
    if ($uploadOk == 0) {
        echo "Sorry, your file was not uploaded.<br>";
    } else {
        // if file already exists, and matches a known MD5, we don't need to overwrite it
        if (file_exists($target_file) && $uploadedFileMD5OK == 1) {
            echo "File uploaded OK!<br>";
            //echo "filename: $target_file <br>";
        } 

        if (!file_exists($target_file)) {
            if ($uploadedFileMD5OK == 0) {
                // if MD5 doesn't match, we can try to use it anyway, but save it with a filename that shows this ROM has an unknown MD5 checksum.
                $target_file = $target_dir . "UNKNOWN-MD5-" . basename($_FILES["fileToUpload"]["name"]);
            }

            if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file)) {
                echo "File uploaded OK!<br>";
                echo "filename: $target_file <br>";
            } else {
                echo "Sorry, something went wrong when uploading your file. Please try again. <br>";
            }
        }
    }
    
    // Then the randomization - if everything went ok
    if ($uploadOk == 1) {
        echo "<html><body>";
        echo "Starting the Randomizationing...<br>";

        if ($_POST["shufflelevels"] == "all")
            $options['Shuffle Levels'] = "all";
        else if ($_POST["shufflelevels"] == "worlds")
            $options['Shuffle Levels'] = "worlds";
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

        if ($_POST["bowserabilities"] == "yes")
            $options['Bowser Abilities'] = "true";
        else if ($_POST["bowserabilites"] == "no")
            $options['Bowser Abilities'] = "false";

        if ($_POST["bowserhitpoints"] == "normal")
            $options['Bowser Hitpoints'] = "normal";
        else if ($_POST["bowserhitpoints"] == "easy")
            $options['Bowser Hitpoints'] = "easy";
        else if ($_POST["bowserhitpoints"] == "medium")
            $options['Bowser Hitpoints'] = "medium";
        else if ($_POST["bowserhitpoints"] == "hard")
            $options['Bowser Hitpoints'] = "hard";

        $options["Mario Color Scheme"] = $_POST["mariocolor"];
        $options["Luigi Color Scheme"] = $_POST["luigicolor"];
        $options["Fire Color Scheme"]  = $_POST["firecolor"];

        $filename = "Super Mario Bros. (JU) [!].nes";
        if ($_POST["seed"])
            $seed = $_POST["seed"];
        else
            $seed = null;

        $options["webmode"] = true;
        smbrMain($target_file, $seed, true);

        echo "</body></html>";
    } else {
        echo "ERRRORRRRR";
    }
}
