<?php

namespace customareas\shape;

use pocketmine\math\Vector3;

class RectangularShape implements Shape{
	/** @var number */
	public $fromx, $tox, $fromz, $toz;
	/** @var string */
	public $levelName;
	public function serialize(){
		$data = ["fx" => $this->fromx, "fz" => $this->fromz, "tox" => $this->tox, "toz" => $this->toz, "lv" => $this->levelName, "v" => 0];
		return serialize($data);
	}
	public function unserialize($d){
		$d = unserialize($d);
		$this->fromx = $d["fx"];
		$this->fromz = $d["fz"];
		$this->tox = $d["tx"];
		$this->toz = $d["tz"];
		$this->levelName = $d["lv"];
	}
	public static function getName(){
		return "rect";
	}
	public function isInside(Vector3 $pos){
		return ($this->fromx <= $pos->x) and ($this->fromz <= $pos->z) and ($this->tox >= $pos->x) and ($this->toz >= $pos->z);
	}
	public function getLevelName(){
		return $this->levelName;
	}
}
