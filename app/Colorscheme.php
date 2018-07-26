<?php namespace SMBR;

class Colorscheme
{
    public $outer;
    public $skin;
    public $inner;

    public function __construct($outer, $skin, $inner)
    {
        $this->outer = $outer;
        $this->skin = $skin;
        $this->inner = $inner;
    }
}

$colorschemes = [
    'random' => new Colorscheme(0, 0, 0),
    'Mario' => new Colorscheme(0x16, 0x27, 0x18),
    'Luigi' => new Colorscheme(0x30, 0x27, 0x19),
    'Vanilla Fire' => new Colorscheme(0x37, 0x27, 0x16),
    'Pale Ninja' => new Colorscheme(0xce, 0xd0, 0x1e),
    'All Black' => new Colorscheme(0x8d, 0x8d, 0x8d),
    'Black & Blue' => new Colorscheme(0xcc, 0x18, 0x2f),
    'Black & Blue 2' => new Colorscheme(0x51, 0xf8, 0x6e),
    'Denim' => new Colorscheme(0x80, 0xa7, 0xcc),
    'Mustard Man' => new Colorscheme(0xd8, 0x27, 0x28),
];
