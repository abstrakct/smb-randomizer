<?php
/*
 * Main website for SMB Randomizer
 */

ini_set('display_errors',1); 
error_reporting(E_ALL);

include "Version.php";

?>

<html>
<body>
<style>
.flex-container {
  display: flex;
}

p {
    font-family: Arial, Helvetica, sans-serif;

}
h1 {
    font-family: Arial, Helvetica, sans-serif;
}
body {
    font-family: Arial, Helvetica, sans-serif;
    font-size: 14px;
}
</style>


<div class="flex-container">

<div style="flex-basis: 800px">
<p>
<h1>Super Mario Bros. Randomizer!</h1>
<?php
echo printVersion() . "<p>";
?>

<form action="/SMBR.php" method="post" enctype="multipart/form-data">
Provide NES file to be randomized:
<input type="file" name="fileToUpload" id="fileToUpload">
<br>
Seed (leave blank for random seed):<br>
<input type="text" name="seed"><br>
<br>
Shuffle levels?<br>
<input type="radio" name="shufflelevels" value="all" checked> All<br>
<input type="radio" name="shufflelevels" value="worlds"> World Order Only<br>
<input type="radio" name="shufflelevels" value="no"> No<br>
<br>
Normal world length?<br>
<input type="radio" name="normalworldlength" value="yes"> Yes<br>
<input type="radio" name="normalworldlength" value="no" checked> No<br>
<br>
Keep or remove pipe transitions?<br>
<input type="radio" name="pipetransitions" value="keep"> Keep<br>
<input type="radio" name="pipetransitions" value="remove" checked> Remove<br>
<br>
Enemy randomization type:<br>
<input type="radio" name="shuffleenemies" value="full" checked> Full<br>
<input type="radio" name="shuffleenemies" value="pools"> Pools<br>
<input type="radio" name="shuffleenemies" value="none"> No enemy shuffle<br>
<br>
Block randomization type:<br>
<input type="radio" name="shuffleblocks" value="all" checked> All<br>
<input type="radio" name="shuffleblocks" value="powerups"> Power-Ups<br>
<input type="radio" name="shuffleblocks" value="grouped"> Grouped<br>
<input type="radio" name="shuffleblocks" value="coins"> Coins<br>
<input type="radio" name="shuffleblocks" value="none"> No block shuffle<br>
<br>
Randomize Bowser's abilities?<br>
<input type="radio" name="bowserabilities" value="yes" checked> Yes<br>
<input type="radio" name="bowserabilities" value="no"> No<br>
<br>
Randomize Bowser's hitpoints?<br>
<input type="radio" name="bowserhitpoints" value="normal"> No<br>
<input type="radio" name="bowserhitpoints" value="easy"> Easy<br>
<input type="radio" name="bowserhitpoints" value="medium"checked> Medium<br>
<input type="radio" name="bowserhitpoints" value="hard"> Hard<br>
<br>
<!-- TODO: generate from Colorscheme.php ! --!>
Color Scheme for Mario:<br>
<select name="mariocolor">
  <option value="random">Totally Random Colors</option><br>
  <option value="Mario">Normal Mario</option><br>
  <option value="Luigi">Normal Luigi</option><br>
  <option value="Vanilla Fire">Normal Fire Mario/Luigi</option><br>
  <option value="Pale Ninja">Pale Ninja</option><br>
  <option value="All Black">All Black</option><br>
  <option value="Black & Blue">Black &amp; Blue</option><br>
  <option value="Black & Blue 2">Black &amp; Blue 2</option><br>
  <option value="Denim">Denim</option><br>
</select>
<br>Color Scheme for Luigi:<br>
<select name="luigicolor">
  <option value="random">Totally Random Colors</option><br>
  <option value="Luigi">Normal Luigi</option><br>
  <option value="Mario">Normal Mario</option><br>
  <option value="Vanilla Fire">Normal Fire Mario/Luigi</option><br>
  <option value="Pale Ninja">Pale Ninja</option><br>
  <option value="All Black">All Black</option><br>
  <option value="Black & Blue">Black &amp; Blue</option><br>
  <option value="Black & Blue 2">Black &amp; Blue 2</option><br>
  <option value="Denim">Denim</option><br>
</select>
<br>Color Scheme for Fire Mario/Luigi:<br>
<select name="firecolor">
  <option value="random">Totally Random Colors</option><br>
  <option value="Vanilla Fire">Normal Fire Mario/Luigi</option><br>
  <option value="Mario">Normal Mario</option><br>
  <option value="Luigi">Normal Luigi</option><br>
  <option value="Pale Ninja">Pale Ninja</option><br>
  <option value="All Black">All Black</option><br>
  <option value="Black & Blue">Black &amp; Blue</option><br>
  <option value="Black & Blue 2">Black &amp; Blue 2</option><br>
  <option value="Denim">Denim</option><br>
</select>
<p>
<input type="submit" value="Let's go!"></input>
</form>
<p>
</div>

<div>
<h1>What is this?</h1>
<p>
Are you tired of playing the same old levels of Super Mario Bros. over and over again? Do you want a challenge while still playing the game you know and love? Well, look no further! This randomizer will take the original Super Mario
Bros. game for the NES and randomize certain elements of the game, kinda like shuffling a deck of cards, providing you with a new and (hopefully) exciting and challenging experience each time! You can even choose various options for which elements are randomized, giving you even more possibilities for variation! 
<br><b><i>Goodbye muscle memory, hello SMB Randomizer!</i></b><br><br>
<i>Please read the Guide, Notes and Bugs/Todo sections before playing for the first time. The randomizer is still in a beta stage, and
although I haven't personally come across an unwinable seed at this point, I cannot guarantee 100% that every seed will produce a winable seed, and there is still room (and plans) for improvement.</i><br>
<p>
<h1>Guide</h1>
<p>
<b>You need to provide a Super Mario Bros. ROM file to be randomized. The recommended ROM is named "Super Mario Bros. (JU) [!].nes", but other versions of SMB might work as well. If you upload a ROM the randomizer doesn't recognize, it might still go ahead and try to use that ROM, but in that case there are no guarantees that the randomized ROM will work correctly!</b>
<p>
<b>Seed</b> is quite self-explanatory: leave empty to get a random seed, or input a number you want to use as the seed.
<p>
<b>Shuffle Levels:</b><br>
<i>All</i> shuffles all levels.<br>
<i>World Order Only</i> shuffles the order in which worlds appear, but keeps the levels within each world in normal order. World 8 will always be last.<br>
<i>No</i> means no shuffling of levels or worlds.
<p>
<b>Normal World Length (only has effect when Shuffle Levels is set to <i>All</i>):</b><br>
<i>Yes</i> means each world will have 4 levels.<br>
<i>No</i> means each world will have a random number of levels. Each world will still end with a castle, and 8-4 will always be the last level of world 8. The total number of levels will be 32, like in the vanilla game. Theoretically a world can have between 1 and 24 levels with this setting. <b>NOTE: This setting does nothing if "Shuffle Levels" is set to <i>no</i>.</b><br>
<p>
<b>Pipe Transitions:</b><br>
Pipe Transitions are the transitions that happen e.g. between 1-1 and 1-2 in the vanilla game.<br>
<i>Remove</i> will remove these transitions.<br>
<i>Keep</i> will keep them (i.e. a pipe transition will show up before vanilla 1-2, wherever vanilla 1-2 is, and so on). <b>NOTE: does NOT work if combined with Shuffle Levels AND Normal World Length set to <i>yes</i></b>! In that case Pipe Transitions will be removed. This is due to limitations in the randomizer code for now. Will hopefully be fixed in the future.<br>
<p>
<b>Enemy Randomization:</b><br>
<i>Full</i> randomizes all enemies, within reasonable limits.<br>
<i>Pools</i> randomizes enemies within smaller pools of similar/related enemies.<br>
<i>No enemy randomization</i> means enemies are NOT randomized in any way.<br>
<p>
<b>Block Randomization:</b><br>
<i>All</i> randomizes all kinds of single blocks that contain an item (mushroom/flower, star, 1-up, coin).<br>
<i>Power-Ups</i> randomizes all single blocks that contain a power-up (mushroom/flower, star, 1-up). Coins are not included in the randomization.<br>
<i>Grouped</i> randomizes single blocks in groups (bricks, question blocks, hidden blocks). <br>
<i>Coins</i> removes ALL power-ups (mushrooms/flowers, stars, 1-ups) and replaces them with coins! Probably quite hard!<br>
<i>No block shuffle</i> means blocks are NOT randomized in any way.<br>
<p>
<b>Bowser's Abilities:</b><br>
<i>Yes</i> randomizes the world in which Bowser starts throwing hammers (between 1 and 7).<br>
<i>No</i> leaves Bowser's abilities unchanged.<br>
<p>
<b>Bowser's Hitpoints</b> randomizes how many hitpoints Bowser has - i.e. how many fireballs it takes to kill him:<br>
<i>No</i> leaves Bowser's hitpoints unchanged at 5.<br>
<i>Easy</i> randomizes Bowser's hitpoints between 1 and 5.<br>
<i>Medium</i> randomizes Bowser's hitpoints between 5 and 10.<br>
<i>Hard</i> randomizes Bowser's hitpoints between 10 and 20.<br>
<p>
<h2>Notes</h2>
<li>The seed you input will always produce the same result, making this randomizer suitable for tournament/race settings. Color schemes, random or not, are independent of this seed/randomization, and does not affect anything gameplay-wise. The same goes for randomized changes of in-game texts.</li>
<li>On the title screen, a "seedhash" is shown where the text "(C) 1985 Nintendo" is normally shown. In a race setting or similar, if all players have the same seedhash it guarantees that the ROMs were generated with the same seed, same settings, same vanilla ROM and same version of the randomizer.</li>
<li>Toad sometimes gets randomized to an enemy - if this enemy kills you on the "Thank you Mario!" screen, don't worry. You don't actually lose a life and the game will progress as normal. You might lose Super/Fire status though, so this needs to be fixed.</li>
<li>Random Colors for Mario/Luigi is totally random, results can be anything from super cool to very weird.</li>
<li>Mosts texts are now randomized. Like color schemes they are randomized independently of anything related to gameplay. An option to don't do this will be added.</li>
<li>Underground bonus areas (when you go down a pipe) are not shuffled, but could be.</li>
<p>
<h2>Bugs / Known Limitations</h2>
<li>The title screen will show whatever is set as the first level, and thus it technically spoils what the first level is. I don't think there's an easy fix for this.</li>
<li>Warp Pipes can be wonky, depending on which world they show up in. The ones that have a number above them work correctly. A pipe in a Warp Zone without a number above it will (probably) take you to level -1 and you'll be stuck there.</li>
<li>Randomized enemies sometimes get stuck inside blocks/walls/pipes. I'm looking into a way to fix this.</li>
<li>Sometimes you get invisible enemies - they are probably hiding behing scenery. This should be fixed.</li>
<li>When "Block Shuffle" is set to <i>All</i> a small number of blocks seem to disappear completely. I haven't figured out why yet.</li>
<li>Trampolines sometimes disappear. If you see that happen, DO NOT jump onto where the trampoline was - you will get stuck if yo do! Instead, look for an alternate way to progress.</li>
<li>Shuffle Levels + Normal World Length + Keep Pipe Transitions = does not work! Can probably be fixed, if there is a high demand for this particular combination...</li>
<li>If Normal World Length is <i>false</i>, there will be no midway points in any level! In other words: No matter where you die on a level, you will respawn at the beginning of the level! This is due to limitations in the original game code.</li> 
<li>Bowser sometimes (very very rarely) disappears/does not spawn. Might be related to there being too many other enemies on screen. Fix is needed.</li>
<p>
<h2>TODO / Upcoming Features / Ideas</h2>
<del><li>Require user to upload a ROM instead of providing one</li></del>
<li>Improve web interface</li>
<del><li>Randomize Bowser's abilities</li></del>
<li>Randomize texts better - by 'texts' I mean strings like this: "thank you mario!" "but our princess is in another castle!" etc.</li>
<li>Add option to not randomize texts.</li>
<li>Add option to randomize what area a pipe takes you to</li>
<li>Fix Toad randomizationing</li>
<li>More error checking</li>
<li>[kinda done] Improve backend</li>
<li>Add option to only randomize clothes for mario/luigi, for more reasonable random colors (hopefully).</li>
<li>Custom color schemes!</li>
<li>More color schemes!</li>
<li>Fix/Randomize Warp Pipes (if possible)</li>
<li>Exclude certain blocks from randomization</li>
<li>Add option to disable warp pipes - if possible</li>
<li>Add option to shuffle all coins/powerups in vanilla in one big pool, so that you in total get the same number of coins/powerups, but don't know where they are</li>
<li>Add option to include continous Cheep-Cheeps/Bullet Bills in randomization pools.</li>
<li>Be more careful about enemy randomization, to avoid despawning a.o. platforms because of too many sprites onscreen. That will probably solve the problem of disappearing trampolines too.
<li>Randomize music? If possible.</li>
<li>Make it an option to have randomized texts independent of game seed?</li>
<li>[kinda done] Better / more readable log</li>
<li>Store a cookie or something when user has uploaded a valid ROM so they don't have to repeat it every time they use the randomizer.</li>
<p>
<h2>Credits / thanks</h2>
<li>Thanks to the SMB3 Randomizer and its author, fcoughlin, for a lot of inspiration, and hours of fun watching and playing SMB3 Rando!</li>
<li>Thanks to the ALTTP Randomizer and the people behind it - for some small bits of code that were taken from there, along with much inspiration! Also, for hours of fun watching and playing!</li>
<li>Various sources of information about the game found online, including, but not limited to:</li>
<li><a href="https://github.com/justinmichaud/rust-nes-emulator/">Rust NES emulator</a></li>
<li><a href="https://gist.github.com/1wErt3r/4048722">A Comprehensive Super Mario Bros. Disassembly</a></li>
<li><a href="https://www.romhacking.net/forum/index.php?topic=25371.0">https://www.romhacking.net/forum/index.php?topic=25371.0</a></li>
<li><a href="https://datacrystal.romhacking.net/wiki/Super_Mario_Bros.:ROM_map">https://datacrystal.romhacking.net/wiki/Super_Mario_Bros.:ROM_map</a></li>


</div>

</body>
</html>
