<?php

/*
 * TODO:
 * pipe pointers! probably the "active world" part.
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

$filename = $argv[1];
$rom = new Rom($filename);
$checksum = $rom->getMD5();
$ok = $rom->checkMD5();

$options['Mario Color Scheme'] = "random";
$options['Luigi Color Scheme'] = "random";
$options['Fire Color Scheme'] = "random";
$options['Remove Pipe Transitions'] = "true";  // remove "pipe transitions" between levels, like between 1-1 and 1-2 in vanilla.
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

$rando = new Randomizer(null, $options, $rom);
$rando->setSeed($rando->getSeed());
$outfilename = "SMBR-" . $rando->getSeed() . ".nes";
$logfilename = "SMBR-" . $rando->getSeed() . ".log";
$log = new Logger($logfilename);
$rom->setLogger($log);
if(!isset($_SESSION['log'])) $_SESSION['log'] = $log;


$rando->makeSeed();
$rando->outputOptions();

//$rom->write(0x1ccc, pack('C*', 0x62));


$rom->save($outfilename);
$log->close();

print("\nFinished!\nFilename: $outfilename\n");

?>
