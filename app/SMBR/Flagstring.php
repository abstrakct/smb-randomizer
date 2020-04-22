<?php 

namespace App\SMBR;

// New flag encoding and decoding algorithm - thanks to Fred Coughlin!
// I've pretty much stolen the entire algorithm from Fred.
// Don't know if he got it from somewhere or came up with it himself.
// It's pretty simple actually.

class Flagstring
{
    protected $flagstring;
    protected $options;

    public function __construct($options = null)
    {
        $this->options = $options;
        $this->optionsToFlagstring($options);
    }

    public function optionsToFlagstring($options = null)
    {
        $alphabet = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';
        //$alphabet = 'MpQa8WoNsBiEd3VuRfCyT2tXgJeZnU4hI7kAlSwOj6DmPxFqL5bGrKv9HzY1c0';
        $flag_string = '';
        $option_values = [
            [config('smbr.randomizer.options.pipeTransitions'), $this->options['pipeTransitions']],
            [config('smbr.randomizer.options.shuffleLevels'), $this->options['shuffleLevels']],
            [config('smbr.randomizer.options.normalWorldLength'), $this->options['normalWorldLength']],
            [config('smbr.randomizer.options.enemies'), $this->options['enemies']],
            [config('smbr.randomizer.options.blocks'), $this->options['blocks']],
            [config('smbr.randomizer.options.bowserAbilities'), $this->options['bowserAbilities']],
            [config('smbr.randomizer.options.bowserHitpoints'), $this->options['bowserHitpoints']],
            [config('smbr.randomizer.options.startingLives'), $this->options['startingLives']],
            [config('smbr.randomizer.options.warpZones'), $this->options['warpZones']],
            [config('smbr.randomizer.options.hiddenWarpDestinations'), $this->options['hiddenWarpDestinations']],
            [config('smbr.randomizer.options.fireworks'), $this->options['fireworks']],
            [config('smbr.randomizer.options.shuffleUndergroundBonus'), $this->options['shuffleUndergroundBonus']],
            [config('smbr.randomizer.options.randomizeBackground'), $this->options['randomizeBackground']],
            [config('smbr.randomizer.options.hardMode'), $this->options['hardMode']],
            [config('smbr.randomizer.options.randomizeUndergroundBricks'), $this->options['randomizeUndergroundBricks']],
            [config('smbr.randomizer.options.excludeFirebars'), $this->options['excludeFirebars']],
            [config('smbr.randomizer.options.randomizeSpinSpeed'), $this->options['randomizeSpinSpeed']],
            [config('smbr.randomizer.options.shuffleSpinDirections'), $this->options['shuffleSpinDirections']],
            [config('smbr.randomizer.options.ohko'), $this->options['ohko']],
        ];
        $flag = 0;

        foreach ($option_values as list($o, $selected)) {
            $selected_index = array_search($selected, array_keys($o)); // TODO: do we need + 1 here?? probably not?
            $flag *= count($o);
            $flag += $selected_index;
            //print("Flag: $flag\n");
        }

        // print("Flag number: " . $flag . "\n");

        $i = 0;
        $alphabet_length = strlen($alphabet);
        do {
            $z = $flag % $alphabet_length;
            // print("Z: $z - flag: " . round($flag) . " - " . $alphabet[$z] . "\n");
            $flag_string[$i] = $alphabet[$z];
            $flag /= $alphabet_length;
            $i++;
        } while ($flag > 1);

        // print("New flag string: $flag_string \n");
        // return strrev($flag_string);
        $this->flagstring = strrev($flag_string);
    }

    public function flagstringToOptions($flag_string, &$options)
    {
        $alphabet = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';
        $alphabet_length = strlen($alphabet);
        $flag_number = 0;
        $option_values = [
            [config('smbr.randomizer.options.ohko'), 'ohko'],
            [config('smbr.randomizer.options.shuffleSpinDirections'), 'shuffleSpinDirections'],
            [config('smbr.randomizer.options.randomizeSpinSpeed'), 'randomizeSpinSpeed'],
            [config('smbr.randomizer.options.excludeFirebars'), 'excludeFirebars'],
            [config('smbr.randomizer.options.randomizeUndergroundBricks'), 'randomizeUndergroundBricks'],
            [config('smbr.randomizer.options.hardMode'), 'hardMode'],
            [config('smbr.randomizer.options.randomizeBackground'), 'randomizeBackground'],
            [config('smbr.randomizer.options.shuffleUndergroundBonus'), 'shuffleUndergroundBonus'],
            [config('smbr.randomizer.options.fireworks'), 'fireworks'],
            [config('smbr.randomizer.options.hiddenWarpDestinations'), 'hiddenWarpDestinations'],
            [config('smbr.randomizer.options.warpZones'), 'warpZones'],
            [config('smbr.randomizer.options.startingLives'), 'startingLives'],
            [config('smbr.randomizer.options.bowserHitpoints'), 'bowserHitpoints'],
            [config('smbr.randomizer.options.bowserAbilities'), 'bowserAbilities'],
            [config('smbr.randomizer.options.blocks'), 'blocks'],
            [config('smbr.randomizer.options.enemies'), 'enemies'],
            [config('smbr.randomizer.options.normalWorldLength'), 'normalWorldLength'],
            [config('smbr.randomizer.options.shuffleLevels'), 'shuffleLevels'],
            [config('smbr.randomizer.options.pipeTransitions'), 'pipeTransitions'],
        ];

        for ($i = 0; $i < strlen($flag_string); $i++) {
            $j = 0;
            for ($j = 0; $j < $alphabet_length && $alphabet[$j] != $flag_string[$i]; $j++);
            $flag_number *= $alphabet_length;
            $flag_number += $j;
        }

        // print("Flag string decoded back to number: $flag_number \n");

        // Now, go through options and set correct choice
        // TODO: improve variable names
        // TODO: understand this algorithm completely!
        foreach ($option_values as list($o, $selected)) {
            $z = count($o);
            $selected_option = $flag_number % $z;
            $flag_number /= $z;
            // print("Selected option: $selected_option \n");

            // Here we need to find out which key in array matches selected_option

            $all_keys = array_keys($o);
            // $all_keys[$selected_option] will now be the option choice we want
            //print_r($selected . " " . $all_keys[$selected_option] . "\n");
            $options[$selected] = $all_keys[$selected_option];
        }
    }

    public function getFlagstring()
    {
        return $this->flagstring;
    }
}
