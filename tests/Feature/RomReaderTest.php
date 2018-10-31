<?php

namespace Tests\Feature;

use SMBR\Rom;
use Tests\TestCase;

class RomReaderTest extends TestCase
{
    /*
     * Read some known bytes from known vanilla ROM
     * Test to assert that the bytes being read are correct
     */
    /** @test */
    public function readKnownBytes()
    {
        $rom = new Rom("roms/Super Mario Bros. (JU) [!].nes");

        // NES Header
        $byte = $rom->read(0, 3);
        $this->assertEquals([0x4E, 0x45, 0x53], $byte);

        // Some randomly chosen bytes
        $byte = $rom->read(0x6b3);
        $this->assertEquals(0x3c, $byte);
        $byte = $rom->read(0xa46);
        $this->assertEquals(0xd4, $byte);
        $byte = $rom->read(0x16b7);
        $this->assertEquals(0x19, $byte);
        $byte = $rom->read(0x1f93);
        $this->assertEquals(0x3b, $byte);
        $byte = $rom->read(0x3072);
        $this->assertEquals(0xb2, $byte);
        $byte = $rom->read(0x4255);
        $this->assertEquals(0xc8, $byte);
        $byte = $rom->read(0x4f0f);
        $this->assertEquals(0xb5, $byte);
        $byte = $rom->read(0x66ad);
        $this->assertEquals(0x99, $byte);

        $byte = $rom->read(0x218b);
        $this->assertEquals(0xcb, $byte);
    }
}
