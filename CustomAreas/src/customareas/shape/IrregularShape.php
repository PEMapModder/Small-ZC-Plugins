<?php

namespace customareas\shape;

use pocketmine\math\Vector2;
use pocketmine\math\Vector3;

class IrregularShape implements Shape, Cached{
	/** @var Vector2[] */
	private $points = [];
	/** @var Line[] */
	private $lines;
	private $levelName;
	/** @var string[] */
	private $cache = [];
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
		return ["p" => serialize(array_map(function(Vector2 $c){
			return "$c->x:$c->y";
		}, $this->points)), "v" => 0];
	}
	public function unserialize($serialized){
		$d = unserialize($serialized)["p"];
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
		$hash = $this->hash($v2);
		if(isset($this->cache[$hash])){
			return $this->cache[$hash];
		}
		$intersects = 0;
		foreach($this->lines as $line){
			$intersects += $line->isAbsolutelyAbove($v2);
		}
		$this->cache[$hash] = $result = (bool) ($intersects & 1);
		return $result;
	}
	public function getLevelName(){
		return $this->levelName;
	}
	public function hash($x, $z = null){
		if($x instanceof Vector2){
			return $this->hash($x->x, $x->y);
		}
		$x = round($x, 1);
		$z = round($z, 1);
		return "$x:$z";
	}
	public function cleanCache(){
		$this->cache = [];
	}
}
