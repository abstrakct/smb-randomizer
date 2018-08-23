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
