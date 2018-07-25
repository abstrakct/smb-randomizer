<?php namespace SMBR;

use \SMBR\Enemy;

class EnemyPools
{
    public $full_enemy_pool;
    public $reasonable_enemy_pool;
    public $toad_pool, $generator_pool, $firebar_pool, $koopa_pool, $goomba_pool, $lakitu_pool, $dont_use;
    public $dont_randomize;

    public function __construct()
    {
        $this->full_enemy_pool = [
            new Enemy('Green Koopa Troopa'),
            new Enemy('Red Koopa Troopa (walks off floors)'),
            new Enemy('Buzzy Beetle'),
            new Enemy('Red Koopa Troopa (stays on floors)'),
            new Enemy('Green Koopa Troopa (does not move)'),
            new Enemy('Hammer Bro'),
            new Enemy('Goomba'),
            new Enemy('Blooper'),
            new Enemy('Bullet Bill'),
            new Enemy('Yellow Koopa Paratroopa (does not move)'),
            new Enemy('Green Cheep-Cheep (slow)'),
            new Enemy('Red Cheep-Cheep (fast)'),
            new Enemy('Podoboo'),
            new Enemy('Pirhana Plant'),
            new Enemy('Green Koopa Paratroopa (leaping)'),
            new Enemy('Red Koopa Troopa (down then up)'),
            new Enemy('Green Koopa Troopa (left then right)'),
            new Enemy('Lakitu'),
            new Enemy('Red Flying Cheep-Cheep Generator'),
            new Enemy('Bowser Fire Generator'),
            new Enemy('Bullet Bill/Cheep-Cheep Generator'),
            new Enemy('Fire Bar (Clockwise)'),
            new Enemy('Fast Fire Bar (Clockwise)'),
            new Enemy('Fire Bar (Counter-Clockwise)'),
            new Enemy('Fast Fire Bar (Counter-Clockwise)'),
            new Enemy('Long Fire Bar (Clockwise)'),
            new Enemy('Bowser'),
            new Enemy('2 Goombas V10'),
            new Enemy('3 Goombas V10'),
            new Enemy('2 Goombas V6'),
            new Enemy('3 Goombas V6'),
            new Enemy('2 Green Koopa Troopas V10'),
            new Enemy('3 Green Koopa Troopas V10'),
            new Enemy('2 Green Koopa Troopas V6'),
            new Enemy('3 Green Koopa Troopas V6'),
        ];

        $this->reasonable_enemy_pool = [
            new Enemy('Green Koopa Troopa'),
            new Enemy('Red Koopa Troopa (walks off floors)'),
            new Enemy('Buzzy Beetle'),
            new Enemy('Red Koopa Troopa (stays on floors)'),
            new Enemy('Green Koopa Troopa (does not move)'),
            new Enemy('Hammer Bro'),
            new Enemy('Goomba'),
            new Enemy('Blooper'),
            new Enemy('Bullet Bill'),
            new Enemy('Yellow Koopa Paratroopa (does not move)'),
            new Enemy('Green Cheep-Cheep (slow)'),
            new Enemy('Red Cheep-Cheep (fast)'),
            new Enemy('Podoboo'),
            new Enemy('Pirhana Plant'),
            new Enemy('Green Koopa Paratroopa (leaping)'),
            new Enemy('Red Koopa Troopa (down then up)'),
            new Enemy('Green Koopa Troopa (left then right)'),
            new Enemy('Lakitu'),
            new Enemy('Bowser Fire Generator'),
            new Enemy('Bullet Bill/Cheep-Cheep Generator'),
            new Enemy('Fire Bar (Clockwise)'),
            new Enemy('Fast Fire Bar (Clockwise)'),
            new Enemy('Fire Bar (Counter-Clockwise)'),
            new Enemy('Fast Fire Bar (Counter-Clockwise)'),
            new Enemy('Long Fire Bar (Clockwise)'),
            new Enemy('2 Goombas V10'),
            new Enemy('3 Goombas V10'),
            new Enemy('2 Goombas V6'),
            new Enemy('3 Goombas V6'),
            new Enemy('2 Green Koopa Troopas V10'),
            new Enemy('3 Green Koopa Troopas V10'),
            new Enemy('2 Green Koopa Troopas V6'),
            new Enemy('3 Green Koopa Troopas V6'),
        ];

//  TODO: rework pools! add to- and from-pools!

        $this->toad_pool = [
            new Enemy('Toad'),
            new Enemy('Lakitu'),
            new Enemy('Podoboo'),
            new Enemy('Hammer Bro'),
            //new Enemy('Pirhana Plant'), coord = 0x9B
            //new Enemy('Green Cheep-Cheep (slow)'), coord = 0x98
            //new Enemy('Red Cheep-Cheep (fast)'), coord 0x98
        ];

        $this->generator_pool = [
            new Enemy('Red Flying Cheep-Cheep Generator'),
            new Enemy('Bowser Fire Generator'),
            new Enemy('Bullet Bill/Cheep-Cheep Generator'),
        ];

        $this->firebar_pool = [
            new Enemy('Fire Bar (Clockwise)'),
            new Enemy('Fast Fire Bar (Clockwise)'),
            new Enemy('Fire Bar (Counter-Clockwise)'),
            new Enemy('Fast Fire Bar (Counter-Clockwise)'),
            new Enemy('Long Fire Bar (Clockwise)'),
            new Enemy('Podoboo'),
        ];

        $this->koopa_pool = [
            new Enemy('Green Koopa Troopa'),
            new Enemy('Red Koopa Troopa (walks off floors)'),
            new Enemy('Red Koopa Troopa (stays on floors)'),
            new Enemy('Green Koopa Troopa (does not move)'),
            new Enemy('Yellow Koopa Paratroopa (does not move)'),
            new Enemy('Green Koopa Paratroopa (leaping)'),
            new Enemy('Red Koopa Troopa (down then up)'),
            new Enemy('Green Koopa Troopa (left then right)'),
            new Enemy('2 Green Koopa Troopas V10'),
            new Enemy('3 Green Koopa Troopas V10'),
            new Enemy('2 Green Koopa Troopas V6'),
            new Enemy('3 Green Koopa Troopas V6'),
            new Enemy('Buzzy Beetle'),
            new Enemy('Hammer Bro'),
            new Enemy('Blooper'),
        ];

        $this->goomba_pool = [
            new Enemy('Goomba'),
            new Enemy('2 Goombas V10'),
            new Enemy('3 Goombas V10'),
            new Enemy('2 Goombas V6'),
            new Enemy('3 Goombas V6'),
            new Enemy('Buzzy Beetle'),
            new Enemy('Hammer Bro'),
            new Enemy('Blooper'),
        ];

        $this->lakitu_pool = [
            new Enemy('Lakitu'),
            new Enemy('Lakitu'),
            new Enemy('Podoboo'),
            new Enemy('Green Koopa Paratroopa (leaping)'),
            new Enemy('Green Koopa Troopa (left then right)'),
            new Enemy('Lakitu'),
            new Enemy('2 Green Koopa Troopas V10'),
            new Enemy('3 Green Koopa Troopas V10'),
            new Enemy('2 Green Koopa Troopas V6'),
            new Enemy('3 Green Koopa Troopas V6'),
            new Enemy('Lakitu'),
        ];

        $this->dont_randomize = [
            new Enemy('Bowser'),
            new Enemy('Lift (Balance)'),
            new Enemy('Lift (Down Up)'),
            new Enemy('Lift (Up)'),
            new Enemy('Lift (Down)'),
            new Enemy('Lift (Left Right)'),
            new Enemy('Lift (Fall)'),
            new Enemy('Lift (Right)'),
            new Enemy('Short Lift (Up)'),
            new Enemy('Short Lift (Down)'),
            new Enemy('Warp Zone'),
        ];

        $this->dont_use = [new Enemy('Spiny')];
    }
}
