<?php

/*
 * Config for the randomizer
 */

return [
    'randomizer' => [
        'defaultOptions' => [
            'colorscheme' => [
                'mario' => 'random',
                'luigi' => 'random',
                'fire' => 'random',
            ],
            'normalWorldLength' => 'true',
            'pipeTransitions' => 'remove',
            'shuffleLevels' => 'all',
            'enemies' => 'randomizeControlled',
            'blocks' => 'randomizeGrouped',
            'bowserHitpoints' => 'random',
            'bowserAbilities' => 'true',
            'startingLives' => 'normal',
            'warpZones' => 'shuffle',
            'hiddenWarpDestinations' => 'false',
            'fireworks' => 'true',
            'shuffleUndergroundBonus' => 'true',
            'randomizeBackground' => 'false',
        ],

        'options' => [
            'colorscheme' => [
                'mario' => [
                    'random' => 'Totally random color scheme',
                    'clothes' => "Randomize the color of Mario's clothes",
                    'Vanilla Mario' => 'Normal Mario color scheme',
                    'Vanilla Luigi' => 'Normal Luigi color scheme',
                    'Vanilla Fire' => 'Normal Fire Mario/Luigi color scheme',
                    'Pale Ninja' => 'Pale Ninja',
                    'All Black' => 'All Black',
                    'Black & Blue' => 'Black & Blue',
                    'Black & Blue 2' => 'Black & Blue 2',
                    'Denim' => 'Denim',
                    'Mustard Man' => 'Mustard Man',
                    'Pretty In Pink' => 'Pretty In Pink',
                ],
                'luigi' => [
                    'random' => 'Totally random color scheme',
                    'clothes' => "Randomize the color of Luigi's clothes",
                    'Vanilla Mario' => 'Normal Mario color scheme',
                    'Vanilla Luigi' => 'Normal Luigi color scheme',
                    'Vanilla Fire' => 'Normal Fire Mario/Luigi color scheme',
                    'Pale Ninja' => 'Pale Ninja',
                    'All Black' => 'All Black',
                    'Black & Blue' => 'Black & Blue',
                    'Black & Blue 2' => 'Black & Blue 2',
                    'Denim' => 'Denim',
                    'Mustard Man' => 'Mustard Man',
                    'Pretty In Pink' => 'Pretty In Pink',
                ],
                'fire' => [
                    'random' => 'Totally random color scheme',
                    'clothes' => 'Random color of clothes only',
                    'Vanilla Mario' => 'Normal Mario color scheme',
                    'Vanilla Luigi' => 'Normal Luigi color scheme',
                    'Vanilla Fire' => 'Normal Fire Mario/Luigi color scheme',
                    'Pale Ninja' => 'Pale Ninja',
                    'All Black' => 'All Black',
                    'Black & Blue' => 'Black & Blue',
                    'Black & Blue 2' => 'Black & Blue 2',
                    'Denim' => 'Denim',
                    'Mustard Man' => 'Mustard Man',
                    'Pretty In Pink' => 'Pretty In Pink',
                ],
            ],
            'pipeTransitions' => [
                'remove' => 'Remove pipe transitions',
                'keep' => 'Keep pipe transitions',
            ],
            'shuffleLevels' => [
                'all' => 'Shuffle all levels',
                'worlds' => 'Shuffle world order only',
                'none' => 'Do not shuffle levels',
            ],
            'normalWorldLength' => [
                'true' => 'Each world has 4 levels',
                'false' => 'Worlds can have varying lengths',
            ],
            'enemies' => [
                'randomizeControlled' => 'Controlled: randomize enemies in a controlled fashion',
                'randomizeChaos' => 'Chaos: randomize all enemies (within reason)',
                'randomizeOld' => 'Randomize enemies within smaller pools of related or similar enemies (old algorithm)',
                'randomizeNone' => 'Do not randomize enemies',
                //    'shufflexxxxxx' => 'NOT IMPLEMENTED: Shuffle Enemies',
            ],
            'blocks' => [
                'randomizeAll' => 'Randomize all blocks',
                'randomizePowerups' => 'Randomize blocks normally containing a power up',
                'randomizeGrouped' => 'Randomize blocks in groups',
                'randomizeCoins' => 'Replace all power ups with coins',
                'randomizeNone' => 'Do not randomize blocks',
                //    'shufflexxxxx' => 'NOT IMPLEMENTED: Shuffle Blocks',
            ],
            'bowserAbilities' => [
                'true' => 'Randomize which worlds Bowser starts throwing hammers and breathing fire',
                'false' => "Do not randomize Bowser's abilities",
            ],
            'bowserHitpoints' => [
                'normal' => 'Bowser has 5 hitpoints',
                'easy' => 'Bowser has 1-5 hitpoints',
                'medium' => 'Bowser has 5-10 hitpoints',
                'hard' => 'Bowser has 10-20 hitpoints',
                'random' => 'Bowser has 1-20 hitpoints',
            ],
            'startingLives' => [
                'very-hard' => 'Very hard: Start with 1 life',
                'normal' => 'Normal: Start with 3 lives',
                'hard' => 'Hard: Start with 1-3 lives',
                'medium' => 'Medium: Start with 3-6 lives',
                'easy' => 'Easy: Start with 6-10 lives',
                'random' => 'Random: Start with 1-19 lives',
            ],
            'warpZones' => [
                'normal' => 'Normal: Warp Zones stay as they are in the original game',
                'random' => 'Random: The destination of warp zone pipes is randomized',
                'shuffle' => 'Shuffle: There will be one warp pipe to each world (2-8), shuffled across the 3 warp zones',
                // TODO: rename useful to allgood
                'useful' => 'Useful: All warp pipes will be useful (take you to a higher world number)',
                // TODO: this is basically "forced warpless"
                'allbad' => 'All bad: All warp pipes will be bad (they will take you to start of current world or earlier)',
                // TODO: easy/hard gamble modes
                'gamble' => 'Gamble: Warp destinations will be hidden, 2 pipes are good, 1 pipe is bad',
                'disable' => 'Disable: Warp pipes will not work at all!',
            ],
            'hiddenWarpDestinations' => [
                'true' => 'Warp Zone destination worlds will not be written above the warp pipes',
                'false' => 'Warp Zone destination worlds will be written above warp pipes as normal',
            ],
            'fireworks' => [
                'true' => 'Randomize when and how many fireworks appear after beating a level',
                'false' => 'Do not randomize fireworks',
            ],
            'shuffleUndergroundBonus' => [
                'true' => 'Shuffle the destinations of pipes going to underground bonus levels',
                'false' => 'Do not shuffle underground bonus levels',
            ],
            'randomizeBackground' => [
                'true' => 'Randomize the background and scenery of levels',
                'false' => 'Do not randomize the background and scenery of levels',
            ],
        ],
    ],
];
