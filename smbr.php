<?php

/*
 *
 * Sources used:
 * https://github.com/justinmichaud/rust-nes-emulator/blob/master/SMAS%20SMB1%20Level%20Data/Level%20Data.pdf
 * http://1st.geocities.jp/bysonshome/smb1/
 */

/*
 * TODO:
 * pipe pointers! probably the "active world" part.
 * disassembly: line 8030
 *
 * randomize bowser's abilities? like hammers etc.?
 * see disasm lines 10218 - 10240 and around there.
 */

require_once "rom.php";
require_once "levels.php";
require_once "randomizer.php";
require_once "logger.php";

if(!session_id()) session_start();

if($argc <= 1) {
    print "Please provide ROM filename.\n";
    exit(1);
}

if($argc > 1) {
    $chosenseed = $argv[2];
    $norandomseed = true;
}

$filename = $argv[1];
$rom = new Rom($filename);
$checksum = $rom->getMD5();
$ok = $rom->checkMD5();

$options['Mario Color Scheme'] = "random";
$options['Luigi Color Scheme'] = "random";
$options['Fire Color Scheme'] = "random";
$options['Remove Pipe Transitions'] = "true";  // remove "pipe transitions" between levels, like between 1-1 and 1-2 in vanilla.
$options['Randomize Levels'] = "false";
$options['Castles Last'] = "true";  // make sure the last level (x-4) of each world is a castle. If set to false, castles can appear anywhere and will take you to the next world when beaten

// rewrite table at 0x1cc4 if removing pipe transitions!

print("ROM filename: $filename\n");
print("MD5 checksum: $checksum");
if($ok) {
    print(" [OK]\n");
} else {
    print(" [FAILED!]\n");
    exit(1);
}

//print("Reading ROM data...");
//$romdata = $rom->read(0, Rom::SIZE);
//print("OK\n");

print("\n");

if($norandomseed) {
    // user has provided a wanted seed, so use that one.
    $rando = new Randomizer($chosenseed, $options, $rom);
} else {
    // pick a random number to be used as the seed
    $rando = new Randomizer(null, $options, $rom);
}
$rando->setSeed($rando->getSeed());
$outfilename = "SMBR-" . $rando->getSeed() . ".nes";
$logfilename = "SMBR-" . $rando->getSeed() . ".log";
$log = new Logger($logfilename);
$rom->setLogger($log);
if(!isset($_SESSION['log'])) $_SESSION['log'] = $log;


$rando->makeSeed();
$rando->outputOptions();

//$rom->write(0x1ccc, pack('C*', 0x62));
//

// this doesn't really work
//$rom->write(0x424a, pack('C*', 0xea));
//$rom->write(0x424b, pack('C*', 0xea));
//$rom->write(0x424c, pack('C*', 0xea));
//$rom->write(0x424d, pack('C*', 0xea));
//$rom->write(0x424e, pack('C*', 0xea));


$rom->save($outfilename);
$log->close();

print("\nFinished!\nFilename: $outfilename\n");

?>
