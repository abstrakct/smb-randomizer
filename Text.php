<?php namespace SMBR;

$messages = [
    // first 3 bytes = header, last = 0x00 - keep those!
    "ThankYouMario" => [ 0xd64, 0xd77 ], // THANK YOU MARIO!
    "ThankYouLuigi" => [ 0xd78, 0xd8b ], // THANK YOU LUIGI!
    "AnotherCastle" => [ 0xd8c, 0xdb7 ], // BUT OUR PRINCESS IS IN \ ANOTHER CASTLE!
    "QuestOver" =>     [ 0xdb8, 0xdce ], // YOUR QUEST IS OVER.
    "NewQuest" =>      [ 0xdcf, 0xded ], // WE PRESENT YOU A NEW QUEST.
    "WorldSelect" =>   [ 0xdee, 0xdfe ], // PUSH BUTTON B
    "WorldSelect2" =>  [ 0xdff, 0xe13 ], // TO SELECT A WORLD
];

$another_castle_variations = [
    "but our princess is in/another castle!",
    "but your eggroll is in/another castle!",
    //"keep on keeping on    / -- bob dylan  ",
];

$thank_you_mario_variations = [
    "thank you mario!",
    "thank you wario!",
    "thank you maria!",
    "thank you sonic!",
    "thank you zelda!",
    "a winner is you!",
];

$thank_you_luigi_variations = [
    "thank you luigi!",
    "thank you wuigi!",
    "thank you mario!",
    "thank you sonic!",
    "thank you link! ",
    "a winner is you!",
];

$quest_over_variations = [
    "your quest is over.",
    "your race is over. ",
    "a winner is you!!!!",
];

$win_variations = [
    [ "your quest is over.", "we present you a new quest.", "push button b", "to select a world" ],
    [ "  A magician was   ", "driving down the road. Then", "  he turned  ", "into a driveway!!" ],
    [ "  I have a step-   ", "  ladder. I never knew my  ", " real ladder.", "                 " ],
    [ " A blind man walks ", "        into a bar.        ", " And a table.", "   And a chair.  " ],
    [ "my dog can do magic", "      tricks. he is a      ", "             ", " labracadabrador." ],
    [ "   I used to be    ", " addicted to soap, but now ", "I am clean...", "                 " ],
    [ "I poured root beer ", "into a square glass. Now I ", "             ", " just have beer. " ],
    [ "     Time flies    ", "       like an arrow.      ", " Fruit flies ", "  like a banana! " ],
    [ " Once upon a time, ", "  men were men and wrote   ", "  their own  ", "   randomizers!  " ],
    [ "                   ", "          SYNTAX           ", "             ", "      ERROR      " ],
];

class Translator {

    public function __construct() {
    }

    public function asciitosmb(string $c) {
        $lettertonumber = [ 
            '.' => 0xaf, ' ' => 0x24, '-' => 0x28, '×' => 0x29, '#' => 0x2A, '!' => 0x2B, '©' => 0xCF, '0' => 0x00, '1' => 0x01, '2' => 0x02, '3' => 0x03,
            '4' => 0x04, '5' => 0x05, '6' => 0x06, '7' => 0x07, '8' => 0x08, '9' => 0x09, 'A' => 0x0A, 'B' => 0x0B, 'C' => 0x0C, 'D' => 0x0D,
            'E' => 0x0E, 'F' => 0x0F, 'G' => 0x10, 'H' => 0x11, 'I' => 0x12, 'J' => 0x13, 'K' => 0x14, 'L' => 0x15, 'M' => 0x16, 'N' => 0x17,
            'O' => 0x18, 'P' => 0x19, 'Q' => 0x1A, 'R' => 0x1B, 'S' => 0x1C, 'T' => 0x1D, 'U' => 0x1E, 'V' => 0x1F, 'W' => 0x20, 'X' => 0x21, 'Y' => 0x22, 'Z' => 0x23 ];
        return $lettertonumber[strtoupper($c)];
    }

    public function smbtoascii(int $c) {
        $numbertoletter = [ 0xaf => '.', 0x24 => ' ', 0x28 => '-', 0x29 => '×', 0x2A => '#', 0x2B => '!', 0xCF => '©', 0x00 => '0', 0x01 => '1', 0x02 => '2', 0x03 => '3', 0x04 => '4', 0x05 => '5', 0x06 => '6', 0x07 => '7', 0x08 => '8', 0x09 => '9', 0x0A => 'A', 0x0B => 'B', 0x0C => 'C', 0x0D => 'D', 0x0E => 'E', 0x0F => 'F', 0x10 => 'G', 0x11 => 'H', 0x12 => 'I', 0x13 => 'J', 0x14 => 'K', 0x15 => 'L', 0x16 => 'M', 0x17 => 'N', 0x18 => 'O', 0x19 => 'P', 0x1A => 'Q', 0x1B => 'R', 0x1C => 'S', 0x1D => 'T', 0x1E => 'U', 0x1F => 'V', 0x20 => 'W', 0x21 => 'X', 0x22 => 'Y', 0x23 => 'Z' ];
        return $numbertoletter[$c];
    }
}
