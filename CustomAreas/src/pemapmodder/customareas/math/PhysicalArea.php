<?php

namespace pemapmodder\customareas;

use pocketmine\level\Level;
use pocketmine\level\Position;
use pocketmine\math\Vector2;

abstract class PhysicalArea{
	public static function newInstance($args, Level $level){
		switch($args["type"]){
			case "rectangle":
				$from = new Vector2($args["start"]["x"], $args["start"]["z"]);
				$to = new Vector2($args["end"]["x"], $args["end"]["z"]);
				return new Rectangle($from, $to, $level);
			case "circle":
				$center = new Vector2($args["center"]["x"], $args["center"]["z"]);
				$radius = $args["radius"];
				return new Circle($center, $radius, $level);
			case "irregular":
				$points = [];
				foreach($args["points"] as $point){
					$points[] = new Vector2($point["x"], $point["z"]);
				}
				return new Irregular($points, $level);
		}
		throw new \InvalidArgumentException("Type ".$args["type"]." not recognized.");
	}
	public abstract function getLevel();
	public abstract function isInside(Position $position);
	public function save(){
		if($this instanceof Rectangle){
			return [
				"start" => ["x" => $this->getFrom()->x, "z" => $this->getFrom()->y],
				"end" => ["x" => $this->getTo()->x, "y" => $this->getFrom()->y]
			];
		}
		if($this instanceof Circle){
			return [
				"center" => ["x" => $this->getCenter()->x, "z" => $this->getCenter()->y],
				"radius" => $this->getRadius()
			];
		}
		if($this instanceof Irregular){
			$points = [];
			foreach($this->getPoints() as $point){
				$points[] = ["x" => $point->x, "z" => $point->y];
			}
			return [
				"points" => $points,
			];
		}
		throw new \InvalidArgumentException("Type ".get_class($this)." cannot be recognized.");
	}
}

