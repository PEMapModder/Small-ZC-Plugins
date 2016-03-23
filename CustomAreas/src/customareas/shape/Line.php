<?php

namespace customareas\shape;

use pocketmine\math\Vector2;

class Line{
	const INTERSECT = 2;
	const ABOVE = 1;
	const BELOW = 0;
	/** @var number */
	private $fromx, $fromz, $tox, $toz;

	public function __construct(Vector2 $from, Vector2 $to){
		$this->fromx = $from->x;
		$this->fromz = $from->y;
		$this->tox = $to->x;
		$this->toz = $to->y;
	}

	public function getSlope(){
		return ($this->toz - $this->fromz) / ($this->tox - $this->fromx);
	}

	public function getZInterceptByX($x0){
		return $this->fromz - ($this->fromx - $x0) * $this->getSlope();
	}

	public function isAbsolutelyAbove(Vector2 $pt){
		$intercept = $this->getZInterceptByX($pt->x);
		if($intercept > $pt->y){
			return self::ABOVE;
		}
		if($intercept === $pt->y){
			return self::INTERSECT;
		}
		return self::BELOW;
	}
}
