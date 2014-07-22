<?php

namespace pemapmodder\customareas;

use pocketmine\level\Level;
use pocketmine\level\Position;
use pocketmine\math\Vector2;

class Circle extends PhysicalArea{
	/** @var Vector2 */
	protected $center;
	/** @var number */
	protected $radius;
	/** @var Level */
	protected $level;
	public function __construct(Vector2 $center, $radius, Level $level){
		$this->center = $center;
		$this->radius = $radius;
		$this->level = $level;
	}
	/**
	 * @return Vector2
	 */
	public function getCenter(){
		return $this->center;
	}
	/**
	 * @return number
	 */
	public function getRadius(){
		return $this->radius;
	}
	/**
	 * @param number $radius
	 */
	public function setRadius($radius){
		$this->radius = $radius;
	}
	public function isInside(Position $position){
		return $position->getLevel() === $this->level and $this->center->distance($position->x, $position->z) <= $this->radius;
	}
	/**
	 * @return Level
	 */
	public function getLevel(){
		return $this->level;
	}
}
