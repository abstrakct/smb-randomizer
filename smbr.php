<?php

/*
 *
 * Sources used:
 * https://github.com/justinmichaud/rust-nes-emulator/
 * http://1st.geocities.jp/bysonshome/smb1/
 */

/*
 * TODO:
 * pipe pointers! probably the "active world" part.
 * disassembly: line 8030
 *
 * randomize bowser's abilities? like hammers etc.?
 * see disasm lines 10218 - 10240 and around there.
 *
 * disasm line 4522+++ = enemy data
 * disasm line 4771+++ = area object data
 *
 *
 * - FLAGS
 * - color schemes
 */

require_once "rom.php";
require_once "levels.php";
require_once "randomizer.php";
require_once "logger.php";
require_once "dump.php";

if(!session_id()) session_start();

if($argc <= 1) {
    print "Please provide ROM filename.\n";
    exit(1);
}

$randomseed = true;
if($argc > 2) {
    $chosenseed = $argv[2];
    $randomseed = false;
}

$filename = $argv[1];
$rom = new Rom($filename);
$checksum = $rom->getMD5();
$ok = $rom->checkMD5();

$options['Mario Color Scheme'] = "random";
$options['Luigi Color Scheme'] = "random";
$options['Fire Color Scheme'] = "random";
$options['Pipe Transitions'] = "remove";  // remove "pipe transitions" between levels, like between 1-1 and 1-2 in vanilla.
$options['Shuffle Levels'] = "false";
$options['Shuffle Enemies'] = "true";
$options['Castles Last'] = "true";  // make sure the last level (x-4) of each world is a castle. If set to false, castles can appear anywhere and will take you to the next world when beaten
// rewrite table at 0x1cc4 if removing pipe transitions!

function testfixpipe() {
}
//$options['debugdump'] = "false";
//if($options['debugdump'] == "true") {
//    // 1-1
//    //levelenemydump(0x1f11, 0x1f2e - 0x1f11 + 1, $rom);
//    //levelobjectdump(0x269e, 0x2702 - 0x269e + 1, $rom);
//    // 1-2
//    //levelobjectdump(0x2c45, 0x2ce7 - 0x2c45 + 1, $rom);
//    levelenemydump(0x20e8, 0x2114 - 0x20e8 + 1, $rom);
//    exit(0);
//}

//levelenemydump(0x2143, 0x216f - 0x2143, $rom);

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

if($randomseed) {
    // pick a random number to be used as the seed
    $rando = new Randomizer(null, $options, $rom);
} else {
    // user has provided a wanted seed, so use that one.
    $rando = new Randomizer($chosenseed, $options, $rom);
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

//$offset = $enemydataoffsets['2-2'];
//levelenemydump($offset, $rom);
//

/*
print("dumping enemies\n\n");

foreach ($enemydataoffsets as $name => $offset) {
    print("$name");
    levelenemydump($offset, $rom);
}
 */

// so this kinda works, at least as proof of concept......
//$pipebyte3 = $rom->read($offset + 2 + 2, 1);
//$newworld = 0;
//$newpipebyte = (($newworld << 5) | ($pipebyte3 & 0x1f));
//$rom->write($offset + 2 + 2, pack('C*', $newpipebyte));
//
//$pipebyte3 = $rom->read($offset + 2 + 5, 1);
//$newworld = 4;
//$newpipebyte = (($newworld << 5) | ($pipebyte3 & 0x1f));
//$rom->write($offset + 2 + 5, pack('C*', $newpipebyte));

$rom->save($outfilename);
$log->close();

print("\nFinished!\nFilename: $outfilename\n");

?>
