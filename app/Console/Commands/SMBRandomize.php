<?php

/*
 * Architecture of this file inspired by / thanks to the VT Alttp randomizer
 */

namespace SMBR\Console\Commands;

use Illuminate\Console\Command;
use SMBR\Logger;
use SMBR\Randomizer;
use SMBR\Rom;

class SMBRandomize extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */

    // TODO: this should, if possible, use default options from config
    protected $signature = 'smbr:randomize {input_file : base rom to randomize}'
        . '{output_dir : where to save the randomized rom}'
        . '{--log=normal : Log level}'
        . '{--savelog=true : Save the log or not}'
        . '{--seed= : set seed for rng}'
        . '{--pipe-transitions=remove : keep or remove pipe transitions}'
        . '{--shuffle-levels=all : level randomization}'
        . '{--normal-world-length=false : world length options}'
        . '{--enemies=randomizeControlled : enemy randomization}'
        . '{--blocks=randomizeGrouped : block randomization}'
        . '{--bowser-abilities=true : randomize Bowser abilities}'
        . '{--bowser-hitpoints=random : randomize Bowser hitpoints}'
        . '{--starting-lives=random : randomize player starting lives}'
        . '{--warp-zones=shuffle : randomize warp zones}'
        . '{--hidden-warp-destinations=false : hidden warp destinations}'
        . '{--fireworks=true : randomize when fireworks appear}'
        . '{--shuffle-underground-bonus=true : shuffle destinations of underground bonus level pipes }'
        . '{--randomize-background=false : randomize the background and scenery of levels }'
        . '{--hard-mode=vanilla : change where secondary hard mode is activated }'
        . '{--randomize-underground-bricks=true : randomize content of brick blocks in underground bonus areas }'
        . '{--exclude-firebars=false : exclude fire bars from enemy randomization }'
        . '{--randomize-spin-speed=false : randomize fire bar spin speeds }'
        . '{--shuffle-spin-directions=false : shuffle fire bar spin directions }'
        . '{--ohko=false : one-hit knock-out mode }'
        . '{--shuffle-music=false : shuffle music }'
        . '{--mariocolors=random : Mario Color Scheme}'
        . '{--luigicolors=random : Luigi Color Scheme}'
        . '{--firecolors=random : Fire Color Scheme}'
    ;

    protected $description = 'Generate a randomized ROM';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        if (!is_readable($this->argument('input_file'))) {
            return $this->error('Input file is not readable.');
        }
        if (!is_dir($this->argument('output_dir'))) {
            return $this->error('Output directory is not a directory.');
        }
        if (!is_writeable($this->argument('output_dir'))) {
            return $this->error('Output directory is not writeable.');
        }

        $smbrOptions['pipeTransitions'] = $this->option('pipe-transitions');
        $smbrOptions['shuffleLevels'] = $this->option('shuffle-levels');
        $smbrOptions['normalWorldLength'] = $this->option('normal-world-length');
        $smbrOptions['enemies'] = $this->option('enemies');
        $smbrOptions['blocks'] = $this->option('blocks');
        $smbrOptions['bowserAbilities'] = $this->option('bowser-abilities');
        $smbrOptions['bowserHitpoints'] = $this->option('bowser-hitpoints');
        $smbrOptions['startingLives'] = $this->option('starting-lives');
        $smbrOptions['warpZones'] = $this->option('warp-zones');
        $smbrOptions['hiddenWarpDestinations'] = $this->option('hidden-warp-destinations');
        $smbrOptions['fireworks'] = $this->option('fireworks');
        $smbrOptions['shuffleUndergroundBonus'] = $this->option('shuffle-underground-bonus');
        $smbrOptions['randomizeBackground'] = $this->option('randomize-background');
        $smbrOptions['hardMode'] = $this->option('hard-mode');
        $smbrOptions['randomizeUndergroundBricks'] = $this->option('randomize-underground-bricks');
        $smbrOptions['excludeFirebars'] = $this->option('exclude-firebars');
        $smbrOptions['randomizeSpinSpeed'] = $this->option('randomize-spin-speed');
        $smbrOptions['shuffleSpinDirections'] = $this->option('shuffle-spin-directions');
        $smbrOptions['ohko'] = $this->option('ohko');
        $smbrOptions['shuffleMusic'] = $this->option('shuffle-music');
        $smbrOptions['mariocolors'] = $this->option('mariocolors');
        $smbrOptions['luigicolors'] = $this->option('luigicolors');
        $smbrOptions['firecolors'] = $this->option('firecolors');
        $smbrOptions['mysterySeed'] = false;
        $this->do_the_randomizer($this->argument('input_file'), $this->argument('output_dir'), $this->option('seed'), $smbrOptions, $this->option('log'), $this->option('savelog'));
    }

    public function do_the_randomizer($input_file, $output_dir, $seed, $options, $logLevel, $saveLog)
    {
        $log = null;

        // global $options, $log;
        //$vanilla = new Game();
        //$vanilla->setVanilla();
        //print_r($vanilla);
        //print(count($vanilla->worlds[1]->levels));

        $rom = new Rom($input_file);
        $checksum = $rom->getMD5();
        $ok = $rom->checkMD5();

        print("\n\nSuper Mario Bros. RANDOMIZER v" . \SMBR\Randomizer::VERSION . "\n\nROM filename: $input_file\n");
        print("MD5 checksum: $checksum");
        if ($ok) {
            print(" [OK]\n");
        } else {
            print(" [FAILED!]\n");
            print("Trying to use this ROM anyway, not guaranteed to work, results may vary...\n");
            //TODO: Add checks to see if ROM is usable (check data in various offsets).
        }

        print("\n");

        // if seed == null a random seed will be chosen, else it will use the user's chosen seed.
        $rando = new Randomizer($seed, $options, $rom);

        // Set the seed, make flags and seedhash
        $rando->setSeed($rando->getSeed());
        $rando->makeFlags();

        // Set filenames
        $outfilename = $output_dir . "/roms/smb-rando-" . $rando->getSeed() . "-" . $rando->flags . ".nes";
        $logfilename = $output_dir . "/logs/smb-rando-" . $rando->getSeed() . "-" . $rando->flags . ".log";

        // Start the logger
        $log = new Logger($logfilename, $saveLog, $logLevel);
        $rom->setLogger($log);
        $rando->setLogger($log);

        // Make seedhash
        $rando->makeSeedHash();

        print("Flagstring: $rando->flags\n");
        print("Seedhash: $rando->seedhash\n");

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
        $log->writeVerbose("\nJSON:\n\n");
        $log->writeVerbose($game_json);
        $log->writeVerbose("\n\n");

        // write "pretty" world layout to logfile
        //$log->write($randomized_game->prettyprint());

        $log->close();

        print("\nFinished!\nFilename: $outfilename\n");
    }
}
