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
            Enemy::get('Green Koopa Troopa'),
            Enemy::get('Red Koopa Troopa (walks off floors)'),
            Enemy::get('Buzzy Beetle'),
            Enemy::get('Red Koopa Troopa (stays on floors)'),
            Enemy::get('Green Koopa Troopa (does not move)'),
            Enemy::get('Hammer Bro'),
            Enemy::get('Goomba'),
            Enemy::get('Blooper'),
            Enemy::get('Bullet Bill'),
            Enemy::get('Yellow Koopa Paratroopa (does not move)'),
            Enemy::get('Green Cheep-Cheep (slow)'),
            Enemy::get('Red Cheep-Cheep (fast)'),
            Enemy::get('Podoboo'),
            Enemy::get('Pirhana Plant'),
            Enemy::get('Green Koopa Paratroopa (leaping)'),
            Enemy::get('Red Koopa Troopa (down then up)'),
            Enemy::get('Green Koopa Troopa (left then right)'),
            Enemy::get('Lakitu'),
            Enemy::get('Red Flying Cheep-Cheep Generator'),
            Enemy::get('Bowser Fire Generator'),
            Enemy::get('Bullet Bill/Cheep-Cheep Generator'),
            Enemy::get('Fire Bar (Clockwise)'),
            Enemy::get('Fast Fire Bar (Clockwise)'),
            Enemy::get('Fire Bar (Counter-Clockwise)'),
            Enemy::get('Fast Fire Bar (Counter-Clockwise)'),
            Enemy::get('Long Fire Bar (Clockwise)'),
            Enemy::get('Bowser'),
            Enemy::get('2 Goombas V10'),
            Enemy::get('3 Goombas V10'),
            Enemy::get('2 Goombas V6'),
            Enemy::get('3 Goombas V6'),
            Enemy::get('2 Green Koopa Troopas V10'),
            Enemy::get('3 Green Koopa Troopas V10'),
            Enemy::get('2 Green Koopa Troopas V6'),
            Enemy::get('3 Green Koopa Troopas V6'),
        ];

        $this->reasonable_enemy_pool = [
            Enemy::get('Green Koopa Troopa'),
            Enemy::get('Red Koopa Troopa (walks off floors)'),
            Enemy::get('Buzzy Beetle'),
            Enemy::get('Red Koopa Troopa (stays on floors)'),
            Enemy::get('Green Koopa Troopa (does not move)'),
            Enemy::get('Hammer Bro'),
            Enemy::get('Goomba'),
            Enemy::get('Blooper'),
            Enemy::get('Bullet Bill'),
            Enemy::get('Yellow Koopa Paratroopa (does not move)'),
            Enemy::get('Green Cheep-Cheep (slow)'),
            Enemy::get('Red Cheep-Cheep (fast)'),
            Enemy::get('Podoboo'),
            Enemy::get('Pirhana Plant'),
            Enemy::get('Green Koopa Paratroopa (leaping)'),
            Enemy::get('Red Koopa Troopa (down then up)'),
            Enemy::get('Green Koopa Troopa (left then right)'),
            Enemy::get('Lakitu'),
            Enemy::get('Bowser Fire Generator'),
            Enemy::get('Bullet Bill/Cheep-Cheep Generator'),
            Enemy::get('Fire Bar (Clockwise)'),
            Enemy::get('Fast Fire Bar (Clockwise)'),
            Enemy::get('Fire Bar (Counter-Clockwise)'),
            Enemy::get('Fast Fire Bar (Counter-Clockwise)'),
            Enemy::get('Long Fire Bar (Clockwise)'),
            Enemy::get('2 Goombas V10'),
            Enemy::get('3 Goombas V10'),
            Enemy::get('2 Goombas V6'),
            Enemy::get('3 Goombas V6'),
            Enemy::get('2 Green Koopa Troopas V10'),
            Enemy::get('3 Green Koopa Troopas V10'),
            Enemy::get('2 Green Koopa Troopas V6'),
            Enemy::get('3 Green Koopa Troopas V6'),
        ];

//  TODO: rework pools! add to- and from-pools!

        $this->toad_pool = [
            Enemy::get('Toad'),
            Enemy::get('Lakitu'),
            Enemy::get('Podoboo'),
            Enemy::get('Hammer Bro'),
            //new Enemy('Pirhana Plant'), coord = 0x9B
            //new Enemy('Green Cheep-Cheep (slow)'), coord = 0x98
            //new Enemy('Red Cheep-Cheep (fast)'), coord 0x98
        ];

        $this->generator_pool = [
            Enemy::get('Red Flying Cheep-Cheep Generator'),
            Enemy::get('Bowser Fire Generator'),
            Enemy::get('Bullet Bill/Cheep-Cheep Generator'),
        ];

        $this->firebar_pool = [
            Enemy::get('Fire Bar (Clockwise)'),
            Enemy::get('Fast Fire Bar (Clockwise)'),
            Enemy::get('Fire Bar (Counter-Clockwise)'),
            Enemy::get('Fast Fire Bar (Counter-Clockwise)'),
            Enemy::get('Long Fire Bar (Clockwise)'),
            Enemy::get('Podoboo'),
        ];

        $this->koopa_pool = [
            Enemy::get('Green Koopa Troopa'),
            Enemy::get('Red Koopa Troopa (walks off floors)'),
            Enemy::get('Red Koopa Troopa (stays on floors)'),
            Enemy::get('Green Koopa Troopa (does not move)'),
            Enemy::get('Yellow Koopa Paratroopa (does not move)'),
            Enemy::get('Green Koopa Paratroopa (leaping)'),
            Enemy::get('Red Koopa Troopa (down then up)'),
            Enemy::get('Green Koopa Troopa (left then right)'),
            Enemy::get('2 Green Koopa Troopas V10'),
            Enemy::get('3 Green Koopa Troopas V10'),
            Enemy::get('2 Green Koopa Troopas V6'),
            Enemy::get('3 Green Koopa Troopas V6'),
            Enemy::get('Buzzy Beetle'),
            Enemy::get('Hammer Bro'),
            Enemy::get('Blooper'),
        ];

        $this->goomba_pool = [
            Enemy::get('Goomba'),
/*             Enemy::get('Red Flying Cheep-Cheep Generator'),
Enemy::get('Bowser Fire Generator'),
Enemy::get('Bullet Bill/Cheep-Cheep Generator'), */
            Enemy::get('2 Goombas V10'),
            Enemy::get('3 Goombas V10'),
            Enemy::get('2 Goombas V6'),
            Enemy::get('3 Goombas V6'),
            Enemy::get('Buzzy Beetle'),
            Enemy::get('Hammer Bro'),
            Enemy::get('Blooper'),
        ];

        $this->lakitu_pool = [
            Enemy::get('Lakitu'),
            Enemy::get('Lakitu'),
            Enemy::get('Podoboo'),
            Enemy::get('Green Koopa Paratroopa (leaping)'),
            Enemy::get('Green Koopa Troopa (left then right)'),
            Enemy::get('Lakitu'),
            Enemy::get('2 Green Koopa Troopas V10'),
            Enemy::get('3 Green Koopa Troopas V10'),
            Enemy::get('2 Green Koopa Troopas V6'),
            Enemy::get('3 Green Koopa Troopas V6'),
            Enemy::get('Lakitu'),
        ];

        $this->dont_randomize = [
            Enemy::get('Bowser'),
            Enemy::get('Lift (Balance)'),
            Enemy::get('Lift (Down Up)'),
            Enemy::get('Lift (Up)'),
            Enemy::get('Lift (Down)'),
            Enemy::get('Lift (Left Right)'),
            Enemy::get('Lift (Fall)'),
            Enemy::get('Lift (Right)'),
            Enemy::get('Short Lift (Up)'),
            Enemy::get('Short Lift (Down)'),
            Enemy::get('Warp Zone'),
        ];

        $this->dont_use = [Enemy::get('Spiny')];
    }
}
