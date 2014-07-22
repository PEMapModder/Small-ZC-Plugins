<?php

namespace pemapmodder\customareas;

use pocketmine\level\Level;
use pocketmine\level\Position;
use pocketmine\math\Vector2;

class Rectangle extends PhysicalArea{
	/** @var Vector2 */
	protected $from, $to;
	/** @var Level */
	protected $level;
	public function __construct(Vector2 $from, Vector2 $to, Level $level){
		$this->from = $from;
		$this->to = $to;
		$this->level = $level;
	}
	public function getLevel(){
		return $this->level;
	}
	public function isInside(Position $position){
		return $position->getLevel() === $this->level and (
			min($this->from->x, $this->to->x) <= $position->x and
			max($this->from->x, $this->to->x) >= $position->x and
			min($this->from->y, $this->to->y) <= $position->z and
			max($this->from->y, $this->to->y) >= $position->z
		);
	}
	/**
	 * @return \pocketmine\math\Vector2
	 */
	public function getFrom(){
		return $this->from;
	}
	/**
	 * @return \pocketmine\math\Vector2
	 */
	public function getTo(){
		return $this->to;
	}
}
