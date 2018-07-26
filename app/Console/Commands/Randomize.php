<?php

/*
 * Architecture of this file inspired by / thanks to the VT Alttp randomizer
 */

namespace SMBR\Console\Commands;

use Illuminate\Console\Command;
use SMBR\Logger;
use SMBR\Randomizer;
use SMBR\Rom;

class Randomize extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'smbr:randomize {input_file : base rom to randomize}'
        . '{output_dir : where to save the randomized rom}'
        . '{--seed= : set seed for rng}'
        . '{--pipe-transitions=remove : keep or remove pipe transitions}'
        . '{--shuffle-levels=all : level randomization}'
        . '{--normal-world-length=false : world length options}'
        . '{--enemies=randomize-full : enemy randomization}'
        . '{--blocks=randomize-all : block randomization}'
        . '{--bowser-abilities=true : randomize Bowser abilities}'
        . '{--bowser-hitpoints=medium : randomize Bowser hitpoints}'
        . '{--starting-lives=normal : randomize player starting lives}'
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

        $smbrOptions['pipe-transitions'] = $this->option('pipe-transitions');
        $smbrOptions['shuffle-levels'] = $this->option('shuffle-levels');
        $smbrOptions['normal-world-length'] = $this->option('normal-world-length');
        $smbrOptions['enemies'] = $this->option('enemies');
        $smbrOptions['blocks'] = $this->option('blocks');
        $smbrOptions['bowser-abilities'] = $this->option('bowser-abilities');
        $smbrOptions['bowser-hitpoints'] = $this->option('bowser-hitpoints');
        $smbrOptions['starting-lives'] = $this->option('starting-lives');
        $smbrOptions['mariocolors'] = $this->option('mariocolors');
        $smbrOptions['luigicolors'] = $this->option('luigicolors');
        $smbrOptions['firecolors'] = $this->option('firecolors');
        $this->do_the_randomizer($this->argument('input_file'), $this->argument('output_dir'), $this->option('seed'), $smbrOptions);
    }

    public function do_the_randomizer($input_file, $output_dir, $seed, $options)
    {
        $log = null;

        // global $options, $log;
        //$vanilla = new Game();
        //$vanilla->setVanilla();
        //print_r($vanilla);
        //print(count($vanilla->worlds[1]->levels));

        $webmode = false;
        $options['webmode'] = false;
        $rom = new Rom($input_file);
        $checksum = $rom->getMD5();
        $ok = $rom->checkMD5();

        if ($webmode) {
            print("<br><br>SMB RANDOMIZER " . printVersion() . "<br><br>ROM filename: $input_file<br>");
            print("MD5 checksum: $checksum");
            if ($ok) {
                print(" <b>[OK]</b><br>");
            } else {
                print(" <b>[FAILED!]</b><br>");
                print("Trying to use this ROM anyway, <b>not guaranteed to work,</b> results may vary...<br>");
                //TODO: Add checks to see if ROM is usable (check data in various offsets).
            }
        } else {
            print("\n\nSMB RANDOMIZER v" . \SMBR\Randomizer::VERSION . "\n\nROM filename: $input_file\n");
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
        $rando = new Randomizer($seed, $options, $rom);

        $rando->setSeed($rando->getSeed());
        $rando->makeFlags();

        if ($webmode) {
            $dir = "webout/" . $rando->getSeed() . "-" . strtoupper($rando->getFlags());
            if (!file_exists($dir)) {
                mkdir($dir, 0744);
            }

            $outfilename = $dir . "/smb-rando-" . $rando->getSeed() . "-" . strtoupper($rando->getFlags()) . ".nes";
            $logfilename = $dir . "/smb-rando-" . $rando->getSeed() . "-" . strtoupper($rando->getFlags()) . ".log";
        } else {
            $outfilename = $output_dir . "/roms/smb-rando-" . $rando->getSeed() . "-" . strtoupper($rando->getFlags()) . ".nes";
            $logfilename = $output_dir . "/logs/smb-rando-" . $rando->getSeed() . "-" . strtoupper($rando->getFlags()) . ".log";
        }

        // Start the logger
        $log = new Logger($logfilename);
        $rom->setLogger($log);
        $rando->setLogger($log);

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
            print('<br><br><b>Finished!</b><br><a href="' . $outfilename . '">Click here to download randomized ROM!</a>');
            print('<br><a href="' . $logfilename . '">Click here to view the log (contains spoilers!)</a>');
        } else {
            print("\nFinished!\nFilename: $outfilename\n");
        }

    }
}
