<?php

require_once "rom.php";
require_once "randomizer.php";

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

print("Randomizing...");

$rando = new Randomizer(null, $options, $rom);
$rando->outputOptions();
$rando->makeSeed($rando->getSeed());

print("\nFinished!\n");

$outfilename = "SMBR-" . $rando->getSeed() . ".nes";
$rom->save($outfilename);

?>
