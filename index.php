<?php
/*
 * Main website for SMB Randomizer
 */

ini_set('display_errors',1); 
error_reporting(E_ALL);





?>
<html>
<body>
<center>
<h1>Super Mario Bros. Randomizer!</h1>

<form action="/~rolf/cgi-bin/smb-randomizer/SMBR.php" method="post">
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
<input type="radio" name="shuffleenemies" value="none"> None<br>
<br>
Block shuffle type:<br>
<input type="radio" name="shuffleblocks" value="all" checked> All<br>
<input type="radio" name="shuffleblocks" value="powerups"> Power-Ups<br>
<input type="radio" name="shuffleblocks" value="grouped"> Grouped<br>
<input type="radio" name="shuffleblocks" value="coins"> Coins<br>
<input type="radio" name="shuffleblocks" value="none"> None<br>
<!-- TODO: generate from Colorscheme.php ! --!>
Color Scheme for Mario:
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
<input type="submit" value="Let's go!">
</form>
<p>

</center>
</body>
</html>
