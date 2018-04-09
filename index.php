<?php
/*
 * Main website for SMB Randomizer
 */

ini_set('display_errors',1); 
error_reporting(E_ALL);


?>

<html>
<body>
<style>
.flex-container {
  display: flex;
}
</style>


<div class="flex-container">
<div>
<h1>Super Mario Bros. Randomizer!</h1>
<form action="/SMBR.php" method="post">
Seed (leave blank for random seed):<br>
<input type="text" name="seed"><br>
<br>
Shuffle levels?<br>
<input type="radio" name="shufflelevels" value="yes" checked> Yes<br>
<input type="radio" name="shufflelevels" value="no"> No<br>
<br>
Normal world length?<br>
<input type="radio" name="normalworldlength" value="yes" checked> Yes<br>
<input type="radio" name="normalworldlength" value="no"> No<br>
<br>
Keep or remove pipe transitions?<br>
<input type="radio" name="pipetransitions" value="keep"> Keep<br>
<input type="radio" name="pipetransitions" value="remove" checked> Remove<br>
<br>
Enemy shuffle type:<br>
<input type="radio" name="shuffleenemies" value="full" checked> Full<br>
<input type="radio" name="shuffleenemies" value="pools"> Pools<br>
<input type="radio" name="shuffleenemies" value="none"> No enemy shuffle<br>
<br>
Block shuffle type:<br>
<input type="radio" name="shuffleblocks" value="all" checked> All<br>
<input type="radio" name="shuffleblocks" value="powerups"> Power-Ups<br>
<input type="radio" name="shuffleblocks" value="grouped"> Grouped<br>
<input type="radio" name="shuffleblocks" value="coins"> Coins<br>
<input type="radio" name="shuffleblocks" value="none"> No block shuffle<br>
<!-- TODO: generate from Colorscheme.php ! --!>
<br>Color Scheme for Mario:
<select name="mariocolor">
  <option value="random">Random</option><br>
  <option value="Mario">Normal</option><br>
  <option value="Pale Ninja">Pale Ninja</option><br>
  <option value="All Black">All Black</option><br>
  <option value="Black & Blue">Black &amp; Blue</option><br>
  <option value="Black & Blue 2">Black &amp; Blue 2</option><br>
  <option value="Denim">Denim</option><br>
</select>
<br>Color Scheme for Luigi:
<select name="luigicolor">
  <option value="random">Random</option><br>
  <option value="Luigi">Normal</option><br>
  <option value="Pale Ninja">Pale Ninja</option><br>
  <option value="All Black">All Black</option><br>
  <option value="Black & Blue">Black &amp; Blue</option><br>
  <option value="Black & Blue 2">Black &amp; Blue 2</option><br>
  <option value="Denim">Denim</option><br>
</select>
<br>Color Scheme for Fire Mario/Luigi:
<select name="firecolor">
  <option value="random">Random</option><br>
  <option value="Vanilla Fire">Normal</option><br>
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
<h1>Guide</h1>
<p>
<b>seed</b> is self-explanatory: leave empty to get a random seed, or input a number you want to use as the seed.
<p>
<b>Shuffle Levels:</b><br>
Select <i>yes</i> if you want the order of the levels to be shuffled.<br>
Select <i>no</i> if you want the order of the levels to be normal.
<p>
<b>Normal World Length:</b><br>
Select <i>yes</i> if you want each world to have 4 levels.<br>
Select <i>no</i> if you want worlds to have a random number of levels. Each world will still end with a castle, and 8-4 will always be the last level of world 8. The total number of levels will be 32, like in the vanilla game. Theoretically a world can have between 1 and 24 levels with this setting.
<p>
<b>Pipe Transitions:</b><br>
Pipe Transitions are the transitions that happen e.g. between 1-1 and 1-2 in the vanilla game.<br>
Select <i>remove</i> if you want to remove these transitions.<br>
Select <i>keep</i> if you want to keep them (i.e. a pipe transition will show up before a level that normally has a pipe transition).
<p>
<b>Enemy Shuffle:</b><br>
<i>Full</i> shuffles all enemies, within reasonable limits.<br>
<i>Pools</i> shuffles enemies within smaller pools of similar/related enemies.<br>
<i>No enemy shuffle</i> means enemies are NOT shuffled in any way.
<p>
<b>Block Shuffle:</b><br>
<i>All</i> shuffles all kinds of blocks that contain an item (mushroom/flower, star, 1-up, coin).<br>
<i>Power-Ups</i> shuffles all blocks that contain a power-up (mushroom/flower, star, 1-up). Coins are not included in the randomzation.<br>
<i>Grouped</i> shuffles blocks in groups - e.g. all bricks that contain an item will now contain a random item.<br>
<i>Coins</i> removes ALL power-ups (mushrooms/flowers, stars, 1-ups) and replaces them with coins! Probably quite hard!<br>
<i>No block shuffle</i> means blocks are NOT shuffled in any way.<br>

<p>


</div>

</body>
</html>
