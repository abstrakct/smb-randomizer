<?php 

namespace App\SMBR;

class Item
{
    public $name = 'Unknown';
    public $object;

    protected static $items;

    public function __construct($n, $o)
    {
        $this->name = $n;
        $this->object = $o;
    }

    public static function get(string $name)
    {
        $items = static::all();
        foreach ($items as $i) {
            if ($i->name == $name) {
                return $i->object;
            }
        }
    }

    public static function getName($object)
    {
        $items = static::all();
        foreach ($items as $i) {
            if ($i->object == $object) {
                return $i->name;
            }
        }
    }

    public static function all()
    {
        if (static::$items) {
            return static::$items;
        }

        static::$items = [
            new Item('Question Block (Mushroom)', 0x00),
            new Item('Question Block (Coin)', 0x01),
            new Item('Hidden Block (Coin)', 0x02),
            new Item('Hidden Block (1-UP)', 0x03),
            new Item('Brick (Mushroom)', 0x04),
            new Item('Brick (Star)', 0x06),
            new Item('Brick (Multiple Coins)', 0x07),
            new Item('Brick (1-UP)', 0x08),
        ];

        return static::all();
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

class ItemPools
{
    public $all_items, $powerups, $all_question_blocks, $all_hidden_blocks, $all_brick_blocks, $all_coins;
    public $exceptions;

    public function __construct()
    {
        $this->all_items = [
            Item::get('Question Block (Mushroom)'),
            Item::get('Question Block (Coin)'),
            Item::get('Hidden Block (Coin)'),
            Item::get('Hidden Block (1-UP)'),
            Item::get('Brick (Mushroom)'),
            Item::get('Brick (Star)'),
            Item::get('Brick (Multiple Coins)'),
            Item::get('Brick (1-UP)'),
        ];
        $this->powerups = [
            Item::get('Brick (Mushroom)'),
            Item::get('Brick (Star)'),
            Item::get('Brick (1-UP)'),
        ];
        $this->all_question_blocks = [
            Item::get('Question Block (Mushroom)'),
            Item::get('Question Block (Coin)'),
        ];
        $this->all_hidden_blocks = [
            Item::get('Hidden Block (Coin)'),
            Item::get('Hidden Block (1-UP)'),
        ];
        $this->all_brick_blocks = [
            Item::get('Brick (Mushroom)'),
            Item::get('Brick (Star)'),
            Item::get('Brick (Multiple Coins)'),
            Item::get('Brick (1-UP)'),
        ];
        $this->all_coins = [
            Item::get('Question Block (Coin)'),
            Item::get('Hidden Block (Coin)'),
            Item::get('Brick (Multiple Coins)'),
        ];
        $this->exceptions = [
            // Hidden coin blocks in 4-2 needed for accessing warp zone.
            0x2d16, 0x2d1a, 0x2d1e, 0x2d20,
            // Hidden coin block in 8-4 needed to complete level normally
            0x2440,
        ];
    }
}
