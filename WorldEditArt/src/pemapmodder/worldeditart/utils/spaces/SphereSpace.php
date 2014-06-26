<?php

namespace pemapmodder\worldeditart\utils\spaces;

use pemapmodder\worldeditart\Main;
use pocketmine\level\Position;
use pocketmine\math\Vector3;

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
		return $out;
	}
	public function getBlockList(){
		$out = [];
		foreach($this->getPosList() as $pos){
			$out[] = $this->centre->getLevel()->getBlock($pos);
		}
		return $out;
	}
	public function isInside(Vector3 $v){
		$out = true;
		$out = ($out and $v->distance($this->centre) <= $this->radius);
		if($v instanceof Position){
			$out = ($out and $v->getLevel()->getName() === $this->centre->getLevel()->getName());
		}
		return $out;
	}
	public function __toString(){
		return "a sphere centered at ".Main::posToStr($this->centre)." of radius {$this->radius}";
	}
}
