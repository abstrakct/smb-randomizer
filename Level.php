<?php namespace SMBR;

class Level {
    protected $name = 'Unknown';
    protected $world;
    protected $map;
    protected $enemy_data_offset;

    public function __construct($name, $map, $edo, $world) {
        $this->name = $name;
        $this->map = $map;
        $this->enemy_data_offset = $edo;
        $this->world = $world;
    }

    public function getName() {
        return $this->name;
    }
}
