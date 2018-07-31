<?php namespace SMBR;

/* require_once "World.php";
require_once "Level.php"; */

use SMBR\Translator;

class DataPacket
{
    private $offset, $data;

    public function __construct($offset, $data)
    {
        $this->offset = $offset;
        $this->data = $data;
    }

    public function getData()
    {
        return $this->data;
    }

    public function getOffset()
    {
        return $this->offset;
    }
};

class Game
{
    public $worlds = [];
    public $midway_points = [];
    public $options;
    private $data_packets = [];

    public function __construct($options)
    {
        $this->options = $options;
    }

    public function setVanillaWorldData()
    {
        foreach ($this->worlds as $world) {
            $world->setVanilla();
        }
    }

    public function resetWorlds()
    {
        unset($this->worlds);
        $this->worlds = [
            new World1(1),
            new World2(2),
            new World3(3),
            new World4(4),
            new World5(5),
            new World6(6),
            new World7(7),
            new World8(8),
        ];
    }

    public function addData($offset, $data)
    {
        $this->data_packets[] = new DataPacket($offset, $data);
    }

    public function getDataPackets()
    {
        return $this->data_packets;
    }

    public function prettyprint()
    {
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
