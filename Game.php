<?php namespace SMBR;

require_once "World.php";
require_once "Level.php";

use SMBR\Translator;

class DataPacket {
    private $offset, $data;

    public function __construct($offset, $data) {
        $this->offset = $offset;
        $this->data = $data;
    }

    public function getData() {
        return $this->data;
    }

    public function getOffset() {
        return $this->offset;
    }
};

class Game {
    public $worlds = [];
    public $midway_points = [];
    private $data_packets = [];

    public function __construct() {
        $this->worlds = [
            '1' => new World1($this, 1),
            '2' => new World2($this, 2),
            '3' => new World3($this, 3),
            '4' => new World4($this, 4),
            '5' => new World5($this, 5),
            '6' => new World6($this, 6),
            '7' => new World7($this, 7),
            '8' => new World8($this, 8),
        ];
    }

    public function setVanilla() {
        foreach ($this->worlds as $world) {
            $world->setVanilla();
        }
    }

    public function addData($offset, $data) {
        $this->data_packets[] = new DataPacket($offset, $data);
    }

    public function getDataPackets() {
        return $this->data_packets;
    }

    public function prettyprint() {
        $trans = new Translator();
        $ret = "\n";
        $ret .= sprintf("WORLD LAYOUT:\n");
        foreach ($this->worlds as $world) {
            $ret .= sprintf($world->getName() . "\n");
            $l = 1;
            foreach ($world->levels as $level) {
                $ret .= sprintf("\t" . $world->num . "-" . $trans->smbtoascii($l) . ": " . $level->name . "\n");
                $l++;
            }
        }
    
        return $ret;
    }
}
