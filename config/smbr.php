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
            'enemies' => 'randomizeFull',
            'blocks' => 'randomizeAll',
            'bowserHitpoints' => 'random',
            'bowserAbilities' => 'true',
            'startingLives' => 'normal',
            'warpZones' => 'shuffle',
            'hiddenWarpDestinations' => 'false',
        ],

        'options' => [
            'colorscheme' => [
                'mario' => [
                    'vanilla' => 'Mario vanilla color scheme',
                    'random' => 'Totally random color scheme',
                ],
                'luigi' => [
                    'vanilla' => 'Luigi vanilla color scheme',
                    'random' => 'Totally random color scheme',
                ],
                'fire' => [
                    'vanilla' => 'Fire Mario/Luigi vanilla color scheme',
                    'random' => 'Totally random color scheme',
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
                'randomizeFull' => 'Randomize all enemies (within reason)',
                'randomizePools' => 'Randomize enemies within smaller pools of related or similar enemies',
                'randomizeNone' => 'Do not randomize enemies',
                'shufflexxxxxx' => 'NOT IMPLEMENTED: Shuffle Enemies',
            ],
            'blocks' => [
                'randomizeAll' => 'Randomize all blocks',
                'randomizePowerups' => 'Randomize blocks normally containing a power up',
                'randomizeGrouped' => 'Randomize blocks in groups',
                'randomizeCoins' => 'Replace all power ups with coins',
                'randomizeNone' => 'Do not randomize blocks',
                'shufflexxxxx' => 'NOT IMPLEMENTED: Shuffle Blocks',
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
                'very-hard' => 'Start with 1 life',
                'normal' => 'Start with 3 lives',
                'hard' => 'Start with 1-3 lives',
                'medium' => 'Start with 3-6 lives',
                'easy' => 'Start with 6-10 lives',
                'random' => 'Start with 1-19 lives',
            ],
            'warpZones' => [
                'normal' => 'Warp Zones stay as they are in the original game',
                'random' => 'The destination of warp zone pipes is randomized',
                'shuffle' => 'There will be one warp pipe to each world, shuffled across the 3 warp zones',
            ],
            'hiddenWarpDestinations' => [
                'true' => 'Warp Zone destination worlds will not be written above the warp pipes',
                'false' => 'Warp Zone destination worlds will be written above warp pipes as normal',
            ],
        ],
    ],
];
