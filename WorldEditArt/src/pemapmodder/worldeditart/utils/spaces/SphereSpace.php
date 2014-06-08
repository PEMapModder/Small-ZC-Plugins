<?php

namespace pemapmodder\worldeditart\utils\spaces;

use pocketmine\level\Position;

class SphereSpace extends Space{
	public function __construct(Position $centre, $radius){
		$this->centre = $centre;
		$this->radius = $radius;
	}
	public function getPosList(){
		$out = [];
		for($x = $this->centre->getX() - $this->radius; $x <= $this->centre->getX() + $this->radius; $x++){
			for($y = $this->centre->getY() - $this->radius; $y <= $this->centre->getY() + $this->radius; $x++){
				for($z = $this->centre->getZ() - $this->radius; $z <= $this->centre->getZ() + $this->radius; $x++){
					$v = new Position($x, $y, $z, $this->centre->getLevel());
					if($v->distance($this->centre) <= $this->radius){
						$out[] = $v;
					}
				}
			}
		}
	}

}
