<?php namespace SMBR;

/**
 * ROM class
 *
 * read and check a rom, and write a new one.
 *
 * inspired by alttp randomizer
 *
 *
 * TODO: Allow changing text?
 */

const LevelListOffset = 0x1ccc;
const LevelCountTableOffset = 0x1cc4;
const MidwayPointsOffset = 0x11cd;

class Rom
{
    const HASH = "811b027eaf99c2def7b933c5208636de";
    const SIZE = 40976;
    private $known_good_hashes = ["811b027eaf99c2def7b933c5208636de", "673913a23cd612daf5ad32d4085e0760"];
    private $hash_to_filename = [
        "811b027eaf99c2def7b933c5208636de" => "uploaded_roms/Super Mario Bros. (JU) [!].nes",
        "673913a23cd612daf5ad32d4085e0760" => "uploaded_roms/Super Mario Bros. (E).nes",
    ];
    const ROMSIZE = 40976;

    private $tmpfile;
    private $log;
    protected $rom;
    protected $level;

    /**
     * constructor
     *
     * @param string $source_location location of source ROM to edit
     *
     * @return void
     */
    public function __construct(string $source_location = null)
    {
        $this->tmpfile = tempnam(sys_get_temp_dir(), __CLASS__);
        if ($source_location !== null) {
            copy($source_location, $this->tmpfile);
        }

        $this->rom = fopen($this->tmpfile, "r+");
    }

    public function setLogger($l)
    {
        $this->log = $l;
    }

    /**
     * Get MD5 hash/checksum of current file
     *
     * @return string
     */
    public function getMD5(): string
    {
        return hash_file('md5', $this->tmpfile);
    }

    /**
     * Check the MD5 hash of current file
     *
     * @return bool
     */
    public function checkMD5(): bool
    {
        return in_array($this->getMD5(), $this->known_good_hashes);
        //return $this->getMD5() === static::HASH;
    }

    /**
     * Read data from the ROM file into an array
     *
     * @param int $offset location in the ROM to begin reading
     * @param int $length data to read
     * // TODO: this should probably always be an array, or a new Bytes object
     * @return array
     */
    public function read(int $offset, int $length = 1)
    {
        // if ($length == 1) {
        //     printf("Reading from %04x - ", $offset);
        // }
        fseek($this->rom, $offset);
        $unpacked = unpack('C*', fread($this->rom, $length));
        // if (count($unpacked) == 1) {
            // printf("Returning %02x\n", $unpacked[1]);
        // }
        return count($unpacked) == 1 ? $unpacked[1] : array_values($unpacked);
    }

    /**
     * Save the changes to this output file
     *
     * @param string $output_location location on the filesystem to write the new ROM.
     *
     * @return bool
     */
    public function save(string $output_location): bool
    {
        return copy($this->tmpfile, $output_location);
    }

    public function b64()
    {
        $data = $this->read(0, self::SIZE);
        return base64_encode(implode(" ", $data));
    }

    public function jsonify()
    {
    }

    /**
     * Write packed data at the given offset
     *
     * @param int $offset location in the ROM to begin writing
     * @param string $data data to write to the ROM
     * @param bool $log write this write to the log
     *
     * @return $this
     */
    public function write(int $offset, string $data, bool $log = true): self
    {
        //if ($log) {
        //    $this->write_log[] = [$offset => array_values(unpack('C*', $data))];
        //}
        //printf("ROM WRITE AT 0x%04x - 0x%02x\n", $offset, $data[0]);
        //print_r($data . "\n");
        fseek($this->rom, $offset);
        fwrite($this->rom, $data);

        $d = array_values(unpack('C*', $data));
        $m = sprintf("rom::write addr: %04x ", $offset);
        $this->log->write($m);
        foreach ($d as $value) {
            $this->log->write(sprintf("%02x ", $value));
        }
        $this->log->write("\n");
        return $this;
    }

    public function writeGame(Game $game)
    {
        $this->log->write("\nWRITING NEW ROM\n");
        $this->log->write("---------------\n");

        $offset = LevelListOffset;
        $index = 0;

        // Write all maps
        foreach ($game->worlds as $world) {
            foreach ($world->levels as $level) {
                $this->write($offset + $index, pack('C*', $level->map));
                $index++;
            }
        }

        // Write the table with how many levels are in each world
        $offset = LevelCountTableOffset;
        $data = 0;
        $index = 0;
        foreach ($game->worlds as $world) {
            $this->write($offset + $index, pack('C*', $data));
            $data += count($world->levels);
            $index++;
        }

        // Write midway points
        // TODO: use data packets?!
        if ($game->options['shuffleLevels'] == "all" || $game->options['shuffleLevels'] == "worlds") {
            $offset = MidwayPointsOffset;
            for ($i = 0; $i < 0xF; $i++) {
                $this->write($offset + $i, pack('C*', $game->midway_points[$i]));
            }
        }

        // Write data packets
        foreach ($game->getDataPackets() as $packet) {
            $this->write($packet->getOffset(), $packet->getData());
        }
    }

    public function setMarioOuterColor(int $color, Game &$game)
    {
        $game->addData(0x005E8, pack('C*', $color));
    }

    public function setMarioSkinColor(int $color, Game &$game)
    {
        $game->addData(0x005E9, pack('C*', $color));
    }

    public function setMarioInnerColor(int $color, Game &$game)
    {
        $game->addData(0x005EA, pack('C*', $color));
    }

    public function setLuigiOuterColor(int $color, Game &$game)
    {
        $game->addData(0x005EC, pack('C*', $color));
    }

    public function setLuigiSkinColor(int $color, Game &$game)
    {
        $game->addData(0x005ED, pack('C*', $color));
    }

    public function setLuigiInnerColor(int $color, Game &$game)
    {
        $game->addData(0x005EE, pack('C*', $color));
    }

    public function setFireOuterColor(int $color, Game &$game)
    {
        $game->addData(0x005F0, pack('C*', $color));
    }

    public function setFireSkinColor(int $color, Game &$game)
    {
        $game->addData(0x005F1, pack('C*', $color));
    }

    public function setFireInnerColor(int $color, Game &$game)
    {
        $game->addData(0x005F2, pack('C*', $color));
    }

    /**
     * destructor
     *
     * @return void
     */
    public function __destruct()
    {
        if ($this->rom) {
            fclose($this->rom);
        }
        unlink($this->tmpfile);
    }
}
