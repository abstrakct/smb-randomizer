<?php

/*
 * Architecture of this file inspired by / thanks to the VT Alttp randomizer
 */

namespace SMBR\Console\Commands;

use Illuminate\Console\Command;

class Randomize extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'smbr:randomize {input_file : base rom to randomize}'
        . '{output_dir : where to save the randomized rom}'
        . '{--testoption : this is a test option}'
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

        if ($this->option('testoption')) {
            echo "Test option applied!";
        }
    }
}
