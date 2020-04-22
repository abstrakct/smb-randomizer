<?php
namespace App\Http\Controllers;

use App\SMBR\Flagstring;
use App\SMBR\Logger;
use App\SMBR\Randomizer;
use App\SMBR\Rom;
use Illuminate\Http\Request;

class RandomizerController extends Controller
{
    public function generate(Request $request)
    {
        $log = null;

        // Save base rom as temp file
        $tmpfilename = tempnam(sys_get_temp_dir(), "SMBR");
        $r = $request->input('rom');
        $romdata = pack('C*', ...$r);
        $tmpfile = fopen($tmpfilename, "w+");
        fwrite($tmpfile, $romdata);
        fclose($tmpfile);

        $rom = new Rom($tmpfilename);

        $mystery = (bool) $request->input('mysterySeed');

        if (!$mystery) {
            // TODO: set options here
            $seed = (int) $request->input('seed');
            $options['pipeTransitions'] = $request->input('pipeTransitions');
            $options['shuffleLevels'] = $request->input('shuffleLevels');
            $options['normalWorldLength'] = $request->input('normalWorldLength');
            $options['enemies'] = $request->input('enemies');
            $options['blocks'] = $request->input('blocks');
            $options['bowserAbilities'] = $request->input('bowserAbilities');
            $options['bowserHitpoints'] = $request->input('bowserHitpoints');
            $options['startingLives'] = $request->input('startingLives');
            $options['warpZones'] = $request->input('warpZones');
            $options['hiddenWarpDestinations'] = $request->input('hiddenWarpDestinations');
            $options['fireworks'] = $request->input('fireworks');
            $options['shuffleUndergroundBonus'] = $request->input('shuffleUndergroundBonus');
            $options['randomizeBackground'] = $request->input('randomizeBackground');
            $options['hardMode'] = $request->input('hardMode');
            $options['randomizeUndergroundBricks'] = $request->input('randomizeUndergroundBricks');
            $options['excludeFirebars'] = $request->input('excludeFirebars');
            $options['randomizeSpinSpeed'] = $request->input('randomizeSpinSpeed');
            $options['shuffleSpinDirections'] = $request->input('shuffleSpinDirections');
            $options['ohko'] = $request->input('ohko');
            $options['mysterySeed'] = false;
        } else {
            // Generate MYSTERY SEED
            // TODO: Check for illegal / impossible combinations!
            $seed = null;
            $options['pipeTransitions'] = array_rand(config('smbr.randomizer.options.pipeTransitions'));
            $options['shuffleLevels'] = array_rand(config('smbr.randomizer.options.shuffleLevels'));
            $options['normalWorldLength'] = array_rand(config('smbr.randomizer.options.normalWorldLength'));
            $options['enemies'] = array_rand(config('smbr.randomizer.options.enemies'));
            $options['blocks'] = array_rand(config('smbr.randomizer.options.blocks'));
            $options['bowserAbilities'] = array_rand(config('smbr.randomizer.options.bowserAbilities'));
            $options['bowserHitpoints'] = array_rand(config('smbr.randomizer.options.bowserHitpoints'));
            $options['startingLives'] = array_rand(config('smbr.randomizer.options.startingLives'));
            $options['warpZones'] = array_rand(config('smbr.randomizer.options.warpZones'));
            $options['hiddenWarpDestinations'] = array_rand(config('smbr.randomizer.options.hiddenWarpDestinations'));
            $options['fireworks'] = array_rand(config('smbr.randomizer.options.fireworks'));
            $options['shuffleUndergroundBonus'] = array_rand(config('smbr.randomizer.options.shuffleUndergroundBonus'));
            // maybe exclude this one...
            $options['randomizeBackground'] = 'false'; //= array_rand(config('smbr.randomizer.options.randomizeBackground'));
            $options['hardMode'] = array_rand(config('smbr.randomizer.options.hardMode'));
            $options['randomizeUndergroundBricks'] = array_rand(config('smbr.randomizer.options.randomizeUndergroundBricks'));
            $options['excludeFirebars'] = array_rand(config('smbr.randomizer.options.excludeFirebars'));
            $options['randomizeSpinSpeed'] = array_rand(config('smbr.randomizer.options.randomizeSpinSpeed'));
            $options['shuffleSpinDirections'] = array_rand(config('smbr.randomizer.options.shuffleSpinDirections'));
            $options['ohko'] = array_rand(config('smbr.randomizer.options.ohko'));
            $options['mysterySeed'] = true;
        }

        $options['shuffleMusic'] = $request->input('shuffleMusic');
        $options['mariocolors'] = $request->input('mario');
        $options['luigicolors'] = $request->input('luigi');
        $options['firecolors'] = $request->input('fire');

        // if seed == null a random seed will be chosen, else it will use the user's chosen seed.
        $rando = new Randomizer($seed, $options, $rom);

        $rando->setSeed($rando->getSeed());
        $rando->makeFlags();

        $romfilename = $request->input('romfilename');

        // Set filenames
        if (!$mystery) {
            $outfilename = "output/roms/" . substr($romfilename, 0, -4) . "_" . $rando->getSeed() . "-" . $rando->flags . ".nes";
            $logfilename = "output/logs/" . substr($romfilename, 0, -4) . "_" . $rando->getSeed() . "-" . $rando->flags . ".log.txt";
            $outfilebasename = substr($romfilename, 0, -4) . "_" . $rando->getSeed() . "-" . $rando->flags . ".nes";
        } else {
            $outfilename = "output/roms/" . substr($romfilename, 0, -4) . "_" . $rando->getSeed() . "-MYSTERY-SEED" . ".nes";
            $logfilename = "output/logs/" . substr($romfilename, 0, -4) . "_" . $rando->getSeed() . "-MYSTERY-SEED" . ".log.txt";
            $outfilebasename = substr($romfilename, 0, -4) . "_" . $rando->getSeed() . "-MYSTERY-SEED" . ".nes";
        }

        // Start the logger
        $log = new Logger($logfilename, $request->input('generateLog'), $request->input('verboseLog') == "true" ? "verbose" : "normal");
        $rom->setLogger($log);
        $rando->setLogger($log);

        // Make seedhash
        $rando->makeSeedHash();

        // Print out the selected options and relevant information
        // $rando->printOptions();

        // Make the seed a.k.a. this performs the actual randomization!!!!
        $randomized_game = $rando->makeSeed();

        // Write all changes (to temporary file)
        $rom->writeGame($randomized_game);

        // Save the new ROM file
        $rom->save($outfilename);

        // write JSON formatted data to logfile
        // $game_json = json_encode($randomized_game, JSON_PRETTY_PRINT);
        // $log->write("\nJSON:\n\n");
        // $log->write($game_json);
        // $log->write("\n\n");

        $log->close();

        $base64data = $rom->b64();

        $responseData = [
            'fullpath' => $outfilename,
            'filename' => $outfilebasename,
            'logfullpath' => $logfilename,
            'base64data' => $base64data,
        ];

        return json_encode($responseData);
    }

    public function generateMysterySeed()
    {

    }

    public function options()
    {
        return config('smbr.randomizer.options');
    }

    public function defaultoptions()
    {
        return config('smbr.randomizer.defaultOptions');
    }

    public function getflags(Request $request)
    {
        $options['pipeTransitions'] = $request->input('pipeTransitions');
        $options['shuffleLevels'] = $request->input('shuffleLevels');
        $options['normalWorldLength'] = $request->input('normalWorldLength');
        $options['enemies'] = $request->input('enemies');
        $options['blocks'] = $request->input('blocks');
        $options['bowserAbilities'] = $request->input('bowserAbilities');
        $options['bowserHitpoints'] = $request->input('bowserHitpoints');
        $options['startingLives'] = $request->input('startingLives');
        $options['warpZones'] = $request->input('warpZones');
        $options['hiddenWarpDestinations'] = $request->input('hiddenWarpDestinations');
        $options['fireworks'] = $request->input('fireworks');
        $options['shuffleUndergroundBonus'] = $request->input('shuffleUndergroundBonus');
        $options['randomizeBackground'] = $request->input('randomizeBackground');
        $options['hardMode'] = $request->input('hardMode');
        $options['randomizeUndergroundBricks'] = $request->input('randomizeUndergroundBricks');
        $options['excludeFirebars'] = $request->input('excludeFirebars');
        $options['randomizeSpinSpeed'] = $request->input('randomizeSpinSpeed');
        $options['shuffleSpinDirections'] = $request->input('shuffleSpinDirections');
        $options['ohko'] = $request->input('ohko');

        $f = new Flagstring($options);
        return $f->getFlagstring();
    }

    public function setOptionsFromFlagstring(Request $request)
    {
        //$rando = new Randomizer(0);
        //$rando->setOptionsFromFlagstring($request->input('flagstring'));
        //return $rando->getOptions();
    }
}
