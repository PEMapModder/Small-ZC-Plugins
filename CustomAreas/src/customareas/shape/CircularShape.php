<?php

namespace customareas\shape;

use pocketmine\math\Vector2;
use pocketmine\math\Vector3;

class CircularShape implements Shape{
	/** @var number */
	private $radius;
	/** @var number */
	private $radiusSquared;
	/** @var number */
	public $centerx, $centerz;
	/** @var string */
	public $levelName;
	public function serialize(){
		return serialize([
			"rad" => $this->radius,
			"x" => $this->centerx,
			"z" => $this->centerz,
			"lv" => $this->levelName
		]);
	}
	public function unserialize($serialized){
		$d = unserialize($serialized);
		$this->radius = $d["rad"];
		$this->centerx = $d["x"];
		$this->centerz = $d["z"];
		$this->levelName = $d["lv"];
	}
	public static function getName(){
		return "circ";
	}
	public function isInside(Vector3 $p){
		return (new Vector2($this->centerx, $this->centerz))->distanceSquared(new Vector2($p->x, $p->z)) <= $this->radiusSquared;
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
		$this->radiusSquared = $radius ** 2;
	}
	public function getLevelName(){
		return $this->levelName;
	}
}
