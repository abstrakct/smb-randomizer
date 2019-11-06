@extends('layouts.master') 
@section('content')
<div id="root">
    <b-container>
        <b-row>
            <b-col>
                <div>
                    <h1>About SMB Randomizer v{!! SMBR\Randomizer::VERSION !!}</h1>
                    <h2>aka useful information before diving in</h2>
                </div>
                <div>
                    <h1>What is this?</h1>
                    <p>
                        Are you tired of playing the same old levels of Super Mario Bros. over and over again? Do you want a challenge while still
                        playing the game you know and love? Well, look no further! This randomizer will take the original
                        Super Mario Bros. game for the NES and randomize certain elements of the game, kinda like shuffling
                        a deck of cards, providing you with a new and (hopefully) exciting and challenging experience each
                        time! You can choose various options for which elements you want to be randomized or shuffled, giving
                        you even more possibilities for variation!
                        <br><b><i>Goodbye muscle memory, hello SMB Randomizer!</i></b><br><br>
                        <i>Please read the Guide, Notes and Bugs/Todo sections before playing for the first time. The randomizer is still in a beta stage, and
although I haven't personally come across an impossible/unwinable seed at this point, I cannot guarantee 100% that every seed will produce a winable seed.</i><br>
                        <p></p>
                        <h1>Who made this?</h1>
                        <p>I go by the name RKaid, or sometimes RKaidgaming. I'm a hobby programmer and a randomizer fan.</p>
                        <h1>Guide</h1>
                        <p></p>
                        <b>You need to provide a Super Mario Bros. ROM file to be randomized. The recommended ROM is named "Super Mario Bros. (JU) [!].nes", but other versions of SMB might work as well. If you upload a ROM the randomizer doesn't recognize, it might still go ahead and try to use that ROM, but in that case there are no guarantees that the randomized ROM will work correctly!</b>
                        <p></p>
                        Most options are rather self-explanatory, but here are some details. Also, the randomizer will let you know if you choose
                        an invalid combination of options.
                        <p></p>
                        <b>Seed</b> is the seed for the RNG. Leave empty to get a random seed, or input a number you want
                        to use as the seed.
                        <p></p>
                        <b>Flags</b> - here you can input a flag string to get all options set based on that flag string.
                        <p></p>
                        <b>Shuffle Levels:</b><br>
                        <b>Shuffle All Levels</b> shuffles, as you might have guessed, all levels. 8-4 will always be the
                        last level.<br>
                        <b>Shuffle World Order Only</b> shuffles the order in which worlds appear, but keeps the levels within
                        each world in normal order. World 8 will always be last.<br>
                        <b>None</b> means no shuffling of levels or worlds.
                        <p></p>
                        <b>Normal World Length (only has effect when Shuffle Levels is set to <i>All</i>):</b><br>
                        <b>Yes</b> means each world will have 4 levels.<br>
                        <b>No</b> means each world will have a random number of levels. Each world will still end with a
                        castle, and 8-4 will always be the last level of world 8. The total number of levels will be 32,
                        like in the vanilla game. Theoretically a world can have between 1 and 24 levels with this setting.
                        <p></p>
                        <b>Pipe Transitions:</b><br> Pipe Transitions are the transitions that happen e.g. between 1-1 and
                        1-2 in the vanilla game.<br>
                        <b>Remove</b> will remove these transitions.<br>
                        <b>Keep</b> will keep them (i.e. a pipe transition will show up before vanilla 1-2, wherever vanilla
                        1-2 is, and so on). <b>NOTE: does NOT work if combined with Shuffle Levels AND Normal World Length set to <i>yes</i></b>!
                        In that case Pipe Transitions will be removed. This is due to limitations in the randomizer code
                        for now. Will hopefully be fixed in the future.<br>
                        <p></p>
                        <b>Warp Zones:</b><br>
                        <b>Normal</b> leaves warp zones unchanged. Can possibly lead to strange results when worlds and levels
                        are all shuffled.<br>
                        <b>Random</b> makes each warp zone pipe have a random destination world. The world number will show
                        above the pipe as normal.<br>
                        <b>Shuffle</b> will make sure one warp pipe to each world (except world 1!) is generated, and shuffle
                        those across the 3 warp zones. There is, however, no guarantee that the warps will be useful! For
                        example, you could find level 4-2 in world 5, but the warp pipes take you to world 2, 3 or 4! The
                        destination of each pipe will still show above the pipe.<br>
                        <b>Useful</b> will make sure all warp pipes are randomized, but useful. That means all warp pipes
                        will take you to a higher world number than you're currently in.<br>
                        <b>Gamble</b> means warp pipe destinations will be hidden, and in the two warp zones that have three
                        pipes, two of the pipes will be useful, and one pipe will be bad. Bad means the pipe will take you
                        to the beginning of your current world, or to an earlier world! Also, the single warp pipe at the
                        end of 4-2 will be a pure gamble - 50/50 good or bad! Are you feeling lucky?
                        <p> </p>
                        <b>Hide Warp Pipe destination worlds</b> - selecting this option will make it so that the game does
                        NOT print what world a warp pipe takes you to above the pipe! The warp pipe itself will function
                        normally. If you select this and warp zone shuffling, the randomizer will make sure that all warp
                        pipes lead to a valid world (no -1 world shenanigans).<br>
                        <p></p>
                        <b>Enemy Randomization:</b><br>
                        <b>Controlled</b> randomizes all enemies, in a fashion where each enemy can only become a subset of enemies, thus making the randomization be within somewhat controlled limits.<br>
                        <b>Chaos</b> randomizes all enemies, within reasonable limits. But still: almost anything goes here, and things can get very difficult/weird/fun!<br>
                        <b>Old</b> randomizes enemies within smaller pools of similar/related enemies. Old algorithm.
                        <br>
                        <b>No enemy randomization</b> means enemies are NOT randomized in any way.
                        <br>
                        <p></p>
                        <b>Block Randomization:</b><br>
                        <b>All</b> randomizes all kinds of single blocks that contain an item (mushroom/flower, star, 1-up,
                        coin). This includes hidden blocks, so it can get kinda weird, and sometimes the hidden blocks don't work for some reason. I'm looking into why.
                        <br>
                        <b>Power-Ups</b> randomizes all brick blocks that contain a power-up (mushroom/flower, star, 1-up). 
                        Coins, powerups in question blocks and hidden 1-UPs are not included in the randomization.<br>
                        <b>Grouped</b> randomizes single blocks in groups (bricks, question blocks, hidden blocks). In other
                        words: any vanilla question block can become a different kind of question block, any hidden block
                        can become a different kind of hidden block, etc. Note that this only applies to single blocks, so
                        rows of e.g. several question blocks do not get randomized.<br>
                        <b>Bricks</b> randomizes the contents of "brick blocks" that have an item (mushroom/flower, star, multiple coins, 1-UP). Question blocks and hidden blocks remain unchanged.<br>
                        <b>Bricks and Question Blocks</b> is like the <i>bricks</i> option, but will also randomize the content of question blocks (mushroom/flower or coin).<br>
                        <b>Coins</b> removes ALL power-ups (mushrooms/flowers, stars, 1-ups) and replaces them with coins!
                        Probably quite hard!<br>
                        <b>No block shuffle</b> means blocks are NOT randomized in any way.<br>
                        <p></p>
                        <b>Bowser's Abilities:</b><br>
                        <b>Yes</b> randomizes the world in which Bowser starts throwing hammers and breathing fire (between
                        1 and 7).<br>
                        <b>No</b> leaves Bowser's abilities unchanged.<br>
                        <p></p>
                        <b>Bowser's Hitpoints</b> randomizes how many hitpoints Bowser has - i.e. how many fireballs it takes
                        to kill him:<br>
                        <b>No</b> leaves Bowser's hitpoints unchanged at 5.<br>
                        <b>Easy</b> randomizes Bowser's hitpoints between 1 and 5.<br>
                        <b>Medium</b> randomizes Bowser's hitpoints between 5 and 10.
                        <br>
                        <b>Hard</b> randomizes Bowser's hitpoints between 10 and 20.
                        <br>
                        <b>Random</b> randomizes Bowser's hitpoints between 1 and 20.
                        <br>
                        <p></p>
                        <b>Starting lives</b> randomizes how many lives the player starts with:<br>
                        <b>Normal</b> leaves lives unchanged at 3.<br>
                        <b>Easy</b> randomizes starting lives between 6-10.<br>
                        <b>Medium</b> randomizes starting lives between 3-6. <br>
                        <b>Hard</b> rndomizes starting lives between 1-3. <br>
                        <b>Random</b> randomizes starting lives between 1 and 19.
                        <p></p>
                        <b>Fireworks</b> randomizes when fireworks appear after beating a level, and how many. The randomizer
                        will select 3 different digits, and if the last digit of your timer is one of those digits when you
                        jump on the flagpole, you will get that many fireworks! Are you ready to win or lose because you
                        or your opponent had to sit through 9 fireworks explosions?<br>
                        <p></p>
                        <b>Underground Bonus Areas</b> - you can shuffle the pipes that take you to an underground bonus area! That means: which underground area you end up in is shuffled around. The pipes that take you there will still be in their normal position.<br>
                        <p></p>
                        <b>Randomize brick blocks in underground bonus areas</b> - select this to randomize the content of the few brick blocks in underground bonus areas that normally have a mushroom or coins.
                        <p></p>
                        <b>Secondary Hard Mode</b> - in vanilla SMB some levels appear two times, and if it appears after 5-3, there will be more enemies on the level. This is sometimes referred to as "secondary hard mode". With this option you can choose to have that always activated (meaning all enemies will spawn on each occurrence of these levels), or you can leave it unchanged. For now, these options do not influence where levels are placed, but there are plans for a mode where you can randomize where this mode starts, and the level shuffling will take this option into account.
                        <p></p>
                        <b>EXPERIMENTAL FEATURE: Randomize background and scenery</b> - this will change the background and scenery of each level. Results can get VERY strange, and there is very little sanity checking for this feature as of now, so there are no guarantees if you select this one. For now it's NOT recommended for races etc.
                        <p></p>
                        <b>OHKO (One-Hit Knock-Out) Mode</b> - if you enable this, any hit will kill Mario/Luigi, even if they are big or have the Fire Flower!
                        <p></p>
                        <b>The "Generate MYSTERY SEED" Button</b> - clicking this button will randomize the randomizer (yo dawg...)! In other words, if you click this the randomizer will randomly choose a setting for each option, and attempt to generate a seed with those options. No spoiler log will be generated, and you will not know which options were chosen! Exciting, yes?
                        WARNING WARNING: At the moment, the "Mystery Seed" mode has very little error checking, so sometimes the randomizer gets stuck trying to generate a seed with options that are incompatible with each other. If this happens, just wait about a minute and try again. This will be fixed! 


                        <p></p>
                        <h2>Notes</h2>
                        <li>The seed you input will always produce the same result, making this randomizer (at least theoretically)
                            suitable for racing, or even a tournament. Color schemes, random or not, are independent of this
                            seed/randomization, and does not affect anything gameplay-wise. The same goes for randomized
                            changes of in-game texts.</li>
                        <li>On the title screen, a "seedhash" is shown above the text "(C) 1985 Nintendo" is normally shown.
                            In a race setting, if all players have the same seedhash it guarantees that the ROMs were generated
                            with the same seed, same settings/flags, same vanilla ROM and same version of the randomizer. The seedhash will also reflect whether or not a spoiler log was generated.</li>
                        <li>Random Colors for Mario/Luigi is totally random, results can be anything from super cool to very
                            weird.
                        </li>
                        <li>Mosts texts are now randomized. Like color schemes they are randomized independently of anything
                            related to gameplay. An option to don't do this will be added.</li>
                        <del><li>Underground bonus areas (when you go down a pipe) are not shuffled, but could be.</li></del>
                        <p></p>
                        <h2>Bugs / Known Limitations</h2>
                        <li>The title screen will show whatever is set as the first level, and thus it technically spoils what
                            the first level is. There's probably no easy fix for this, but it's possible to change it so
                            that Mario doesn't start autowalking, so that will be added in a future version.</li>
                        <del><li>Warp Pipes (if left unchanged) can be wonky, depending on which world they show up in. The ones that
                            have a number above them work correctly. A pipe in a Warp Zone without a number above it will
                            (probably) take you to world -1 and you'll be stuck there. A fix is coming for this.</li></del>
                        <li>Randomized enemies sometimes (not often) get stuck inside blocks/walls/pipes. This will be fixed.</li>
                        <del><li>The enemy randomization algorithm is not very good at this point, and will be improved!</li></del>
                        <del><li>Sometimes (very rarely) you can get invisible enemies - they are probably hiding behing scenery.
                            This should be fixed, if it still happens.</li></del>
                        <li>When "Block Shuffle" is set to <i>All</i> a small number of blocks seem to disappear completely.
                            I looking into why.</li>
                        <li>Trampolines sometimes disappear. If you see that happen, DO NOT jump onto where the trampoline was
                            - you will get stuck if yo do! Instead, look for an alternate way to progress. UPDATE: It's been a long time since I've seen this happen, it might be fixed now as it probably was a side-effect of something else.</li>
                        <li>Shuffle Levels + Normal World Length + Keep Pipe Transitions = does not work! Can probably be fixed,
                            if there is a high demand for this particular combination...
                        </li>
                        <li>If Normal World Length is <i>false</i>, there will be no midway points in any level! In other words:
                            No matter where you die on a level, you will respawn at the beginning of the level! This is due
                            to limitations in the original game code, and a fix would require some serious romhacking.</li>
                        <li>Bowser sometimes (very very rarely) disappears/does not spawn. Might be related to there being too
                            many other enemies on screen. This must be fixed. UPDATE: I haven't seen this happen in a long time, it might be fixed.</li>
                        <p></p>
                        
                        <!--
                        <h2>TODO / Upcoming Features / Ideas</h2>
                        <del><li>Require user to upload a ROM instead of providing one</li></del>
                        <del><li>Improve web interface</li></del>
                        <del><li>Randomize Bowser's abilities</li></del>
                        <del> <li>Fix/Randomize Warp Pipes</li></del>
                        <del><li>Add option to not generate spoiler/log.</li></del>
                        <li>Exclude certain blocks from randomization.</li>
                        <li>Improve block randomization algorithm.</li>
                        <li>MOSTLY DONE: Improve enemy randomization algorithm.</li>
                        <li>Be more careful about enemy randomization, to avoid despawning a.o. platforms because of too many
                            sprites onscreen. That will probably solve the problem of disappearing trampolines too.
                        </li>
                        <li>Add option to randomize music.</li>
                        <li>Add option to disable warp pipes - if possible</li>
                        <li>Add option to shuffle all coins/powerups in vanilla in one big pool, so that you in total get the
                            same number of coins/powerups, but don't know where they are</li>
                        <li>Option to start game in hard mode, i.e. as the game is after you have won once. In this case we might
                            want to disable the world select function.</li>
                        <li>Improve this about page!</li>
                        <li>Randomize texts better?</li>
                        <li>Add option to not randomize texts.</li>
                        <li>Add option to randomize what area a pipe takes you to</li>
                        <li>More error checking</li>
                        <li>[quite done] Improve backend</li>
                        <del><li>Add option to only randomize clothes for mario/luigi, for more reasonable random colors (hopefully).</li></del>
                        <li>Custom color schemes!</li>
                        <li>More color schemes!</li>
                        <li>Add option to include continous Cheep-Cheeps/Bullet Bills in randomization pools.</li>
                        <li>Make it an option to have randomized texts independent of game seed?</li>
                        <li>Many other ideas and possibilites!</li>
                        <del><li>[kinda done] Better / more readable log</li></del>
                        <del><li>Store a cookie or something when user has uploaded a valid ROM so they don't have to repeat it every
                            time they use the randomizer. Note: base rom gets stored in your browser's local storage.</li></del>
                        <p></p>
                        -->
                        <h2>Credits / thanks</h2>
                        <li>Thanks to the SMB3 Randomizer and its author, fcoughlin, for a lot of inspiration, and hours of fun
                            watching and playing SMB3 Rando!</li>
                        <li>Thanks to the ALTTP Randomizer and the people behind it - for some small bits of code that were taken
                            from there, along with much inspiration! Also, for hours of fun watching and playing!</li>
                        <li>Various sources of information about the game found online, including, but not limited to:</li>
                        <li><a href="https://github.com/justinmichaud/rust-nes-emulator/">Rust NES emulator</a></li>
                        <li><a href="https://gist.github.com/1wErt3r/4048722">A Comprehensive Super Mario Bros. Disassembly</a></li>
                        <li><a href="https://www.romhacking.net/forum/index.php?topic=25371.0">https://www.romhacking.net/forum/index.php?topic=25371.0</a></li>
                        <li><a href="https://datacrystal.romhacking.net/wiki/Super_Mario_Bros.:ROM_map">https://datacrystal.romhacking.net/wiki/Super_Mario_Bros.:ROM_map</a></li>


                </div>

            </b-col>
        </b-row>
    </b-container>
</div>
@endsection