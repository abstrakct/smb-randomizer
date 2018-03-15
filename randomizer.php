<?php

/*
 * The main randomizer class!
 *
 * Again inspired by the ALttP Randomizer.
 *
 * Uses mt_rand like the ALttP Randomizer.
 *
 * Developed on php 7.2 - using 7.1 can produce different random numbers!
 * http://php.net/manual/en/migration72.incompatible.php#migration72.incompatible.rand-mt_rand-output
 */

class Randomizer {
    protected $rng_seed;
    protected $seed;
    protected $options;
    protected $rom;

    /**
     * Create a new randomizer.
     *
     * TODO: error checking etc.
     *
     * @param int seed seed to use for RNG
     * @param array opt options for randomization
     * @param Rom rom the rom object to modify
     *
     * @return void
     */
    public function __construct($seed = 1, $opt = null, $rom = null) {
        $this->rng_seed = $seed;
        $this->options = $opt;
        $this->rom = $rom;
    }

    public function outputOptions() {
        print("\n\n*** SETTINGS ***\nSeed: $this->rng_seed\n");

        foreach ($this->options as $key => $value) {
            print("$key: $value\n");
        }
    }

    public function getSeed() {
        return $this->rng_seed;
    }

        // Here we go!
    public function makeSeed(int $rng_seed = null) {
        $rng_seed = $rng_seed ?: random_int(1, 999999999); // cryptographic pRNG for seeding
		$this->rng_seed = $rng_seed % 1000000000;
		mt_srand($this->rng_seed);

        print("\nOK - making randomized SMB ROM with seed $this->rng_seed\n");

        $this->setMarioColorScheme($this->options['Mario Color Scheme']);
        $this->setLuigiColorScheme($this->options['Luigi Color Scheme']);
        $this->setFireColorScheme($this->options['Fire Color Scheme']);
    }

    public function setMarioColorScheme(string $colorscheme) : self {
        if($colorscheme == "normal") {
            return $this;
        }
        if($colorscheme == "random") {
            $outer = mt_rand(0, 255);
            $skin = mt_rand(0, 255);
            $inner = mt_rand(0, 255);
            $this->rom->setMarioInnerColor($inner);
            $this->rom->setMarioSkinColor($skin);
            $this->rom->setMarioOuterColor($outer);
        }
        return $this;
    }

    public function setFireColorScheme(string $colorscheme) : self {
        if($colorscheme == "normal") {
            return $this;
        }
        if($colorscheme == "random") {
            $outer = mt_rand(0, 255);
            $skin = mt_rand(0, 255);
            $inner = mt_rand(0, 255);
            $this->rom->setFireInnerColor($inner);
            $this->rom->setFireSkinColor($skin);
            $this->rom->setFireOuterColor($outer);
        }
        return $this;
    }

    public function setLuigiColorScheme(string $colorscheme) : self {
        if($colorscheme == "normal") {
            return $this;
        }
        if($colorscheme == "random") {
            $outer = mt_rand(0, 255);
            $skin = mt_rand(0, 255);
            $inner = mt_rand(0, 255);
            $this->rom->setLuigiInnerColor($inner);
            $this->rom->setLuigiSkinColor($skin);
            $this->rom->setLuigiOuterColor($outer);
        }
        return $this;
    }
}


?>
