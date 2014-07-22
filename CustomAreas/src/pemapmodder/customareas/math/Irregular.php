<?php

namespace pemapmodder\customareas;

use pocketmine\level\Position;
use pocketmine\math\Vector2;
use pocketmine\level\Level;

class Irregular extends PhysicalArea{
	protected $lines = [];
	protected $points;
	protected $level;
	/**
	 * @param Vector2[] $points
	 * @param Level $level
	 * @throws \InvalidArgumentException
	 */
	public function __construct(array $points, Level $level){
		if(count($points) < 3){
			throw new \InvalidArgumentException("A shape is made up of at least three points.");
		}
		for($i = 1; $i < count($points); $i++){
			$this->lines[] = new ConstructedLine($points[$i - 1], $points[$i]);
		}
		$this->points = $points;
		$this->level = $level;
	}
	public function getLevel(){
		return $this->level;
	}
	public function isInside(Position $position){
		if($this->level !== $position->getLevel()){
			return false;
		}
		for($i = 1; $i < count($this->lines); $i++){
			if(!ConstructedLine::isBetween($this->lines[$i - 1], $this->lines[$i], new Vector2($position->x, $position->z))){
				return false;
			}
		}
		return true;
	}
	/**
	 * @return Vector2[]
	 */
	public function getPoints(){
		return $this->points;
	}
}
