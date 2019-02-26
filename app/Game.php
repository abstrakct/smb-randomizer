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
            new World1(0),
            new World2(1),
            new World3(2),
            new World4(3),
            new World5(4),
            new World6(5),
            new World7(6),
            new World8(7),
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
                    $ret .= "\t";
                if ($level->name == "Pipe Transition") {
                    $ret .= sprintf("Pipe Transition\n");
                } else {
                    $ret .= sprintf($world->num + 1 . "-" . $trans->smbtoascii($l) . ": " . $level->name . "\n");
                    $l++;
                }
            }
        }

        return $ret;
    }
}
