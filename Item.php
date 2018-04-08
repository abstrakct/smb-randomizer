<?php namespace SMBR;

class Item {
    public $name = 'Unknown';
    public $object;

    public function __construct($n, $o) {
        $this->name = $n;
        $this->object = $o;
    }

    public function getName() {
        return $this->name;
    }
}

//$all_items = [
//    new Item("Question Block (Mushroom)", 0x00),
//    new Item("Question Block (Coin)",     0x01),
//    new Item("Hidden Block (Coin)",       0x02),
//    new Item("Hidden Block (1-UP)",       0x03),
//    new Item("Brick (Mushroom)",          0x04),
//    new Item("Brick (Star)",              0x06),
//    new Item("Brick (Multiple Coins)",    0x07),
//    new Item("Brick (1-UP)",              0x08),
//];
//
//$question_items = [
//    new Item("Question Block (Mushroom)", 0x00),
//    new Item("Question Block (Coin)",     0x01),
//];
//
//$hidden_items = [
//    new Item("Hidden Block (Coin)",       0x02),
//    new Item("Hidden Block (1-UP)",       0x03),
//];
//
//$brick_items = [
//    new Item("Brick (Mushroom)",          0x04),
//    new Item("Brick (Star)",              0x06),
//    new Item("Brick (Multiple Coins)",    0x07),
//    new Item("Brick (1-UP)",              0x08),
//];

$all_items = [ 0x00, 0x01, 0x02, 0x03, 0x04, 0x06, 0x07, 0x08 ];
$powerups  = [ 0x00, 0x03, 0x04, 0x06, 0x08 ];
$all_question_blocks = [ 0x00, 0x01 ];
$all_hidden_blocks   = [ 0x02, 0x03 ];
$all_brick_blocks    = [ 0x04, 0x06, 0x07, 0x08 ];
$all_coins = [ 0x01, 0x02, 0x07 ];
