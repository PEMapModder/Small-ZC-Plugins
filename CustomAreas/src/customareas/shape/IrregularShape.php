<?php

namespace customareas\shape;

use pocketmine\math\Vector2;
use pocketmine\math\Vector3;

class IrregularShape implements Shape{
	/** @var Vector2[] */
	private $points = [];
	/** @var Line[] */
	private $lines;
	private $levelName;
	/**
	 * @return \pocketmine\math\Vector2[]
	 */
	public function getPoints(){
		return $this->points;
	}
	/**
	 * @param Vector2[] $points
	 */
	public function setPoints($points){
		if(count($points) < 3){
			throw new \InvalidArgumentException("A shape must have at least 3 points");
		}
		$this->points = array_values($points);
		$this->recalculateLines();
	}
	private function recalculateLines(){
		$this->lines = [];
		foreach($this->points as $i => $pt){
			$this->lines[$i] = new Line($pt, isset($this->points[$i + 1]) ? $this->points[$i + 1] : $this->points[0]);
		}
	}
	public function serialize(){
		return serialize(array_map(function(Vector2 $c){
			return "$c->x:$c->y";
		}, $this->points));
	}
	public function unserialize($serialized){
		$d = unserialize($serialized);
		$this->setPoints(array_map(function($str){
			list($x, $y) = explode(":", $str);
			return new Vector2($x, $y);
		}, $d));
	}
	public static function getName(){
		return "ireg";
	}
	public function isInside(Vector3 $p){
		$v2 = new Vector2($p->x, $p->z);
		$intersects = 0;
		foreach($this->lines as $line){
			$intersects += $line->isAbsolutelyAbove($v2);
		}
		return (bool) ($intersects & 1);
	}
	public function getLevelName(){
		return $this->levelName;
	}
}
