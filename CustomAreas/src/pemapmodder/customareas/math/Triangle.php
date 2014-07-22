<?php

namespace pemapmodder\customareas;

use pocketmine\level\Level;
use pocketmine\level\Position;
use pocketmine\math\Vector2;

class Triangle extends PhysicalArea{
	protected $lines;
	protected $level;
	public function __construct(Vector2 $a, Vector2 $b, Vector2 $c, Level $level){
		$this->lines = [
			new ConstructedLine($a, $b),
			new ConstructedLine($b, $c),
			new ConstructedLine($a, $c)
		];
		$this->level = $level;
	}
	public function getLevel(){
		return $this->level;
	}
	public function isInside(Position $position){
		if($position->getLevel() !== $this->level){
			return false;
		}
		for($i = 0; $i < 3; $i++){
			$o0 = ($i + 1) % 3;
			$o1 = ($i + 2) % 3;
			if(!ConstructedLine::isBetween($this->lines[$o0], $this->lines[$o1], new Vector2($position->x, $position->z))){
				return false;
			}
		}
		return true;
	}
}
