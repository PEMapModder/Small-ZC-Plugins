<?php

namespace pemapmodder\customareas;

use pocketmine\math\Vector2;

class ConstructedLine{
	protected $passingPoint;
	/**
	 * @param Vector2 $passingPoint
	 * @param Vector2|number $arg2
	 */
	public function __construct(Vector2 $passingPoint, $arg2){
		$slope = $arg2;
		if($arg2 instanceof Vector2){
			$slope = self::getMathSlope($passingPoint, $arg2);
		}
		$this->slope = $slope;
		$this->passingPoint = $passingPoint;
	}
	public function getSlope(){
		return $this->slope;
	}
	public function getYIntercept(){
		return $this->passingPoint->y - $this->slope * $this->passingPoint->x;
		// y = mx + c
		// c = y - mx
	}
	public function isOn(Vector2 $point){
		return $this->slope === self::getMathSlope($point, $this->passingPoint);
	}
	public function isAbove(Vector2 $point){
		$other = new ConstructedLine($point, $this->slope);
		return $other->getYIntercept() > $this->getYIntercept();
	}
	public static function getMathSlope(Vector2 $a, Vector2 $b){
		return ($a->y - $b->y) / ($a->x - $b->x);
	}
	public static function isBetween(ConstructedLine $l1, ConstructedLine $l2, Vector2 $point){
		return ($l1->isAbove($point) xor $l2->isAbove($point)) or $l1->isOn($point) or $l2->isOn($point); // first time using xor :P
	}
}
