<?php

namespace pemapmodder\worldeditart\utils\spaces;

use pemapmodder\worldeditart\Main;
use pocketmine\level\Position;
use pocketmine\math\Vector3;

class CylinderSpace extends Space{
	const X = 0, Y = 1, Z = 2;
	/** @var Position*/
	private $base;
	/** @var int positive integers */
	private $height, $radius;
	/** @var int */
	private $axis;
	public function __construct($axis, $radius, Position $base, $height){
		$this->base = $base;
		$this->height = $height;
		$this->radius = $radius;
		$this->axis = $axis % 3;
	}
	public function getPosList(){
		$out = [];
		switch($this->axis){
			case self::X:
				for($i = 0; $i < $this->height; $i++){
					$x = $this->base->getFloorX() + $i;
					$centre = new Vector3($x, $this->base->getFloorY(), $this->base->getFloorZ());
					for($y = $this->base->getFloorY() - $this->radius; $y <= $this->base->getFloorY() + $this->radius; $y++){
						for($z = $this->base->getFloorZ() - $this->radius; $z <= $this->base->getFloorZ() + $this->radius; $z++){
							$v = new Vector3($x, $y, $z);
							if($centre->distance($v) <= $this->radius){
								$out[] = $this->base->getLevel();
							}
						}
					}
				}
				break;
			case self::Y:
				for($i = 0; $i < $this->height; $i++){
					$y = $this->base->getFloorY() + $i;
					$centre = new Vector3($this->base->getFloorX(), $y, $this->base->getFloorZ());
					for($x = $this->base->getFloorX() - $this->radius; $x <= $this->base->getFloorX() + $this->radius; $x++){
						for($z = $this->base->getFloorZ() - $this->radius; $z <= $this->base->getFloorZ() + $this->radius; $z++){
							$v = new Position($x, $y, $z, $this->base->getLevel());
							if($centre->distance($v) <= $this->radius){
								$out[] = $v;
							}
						}
					}
				}
				break;
			case self::Z:
				for($i = 0; $i < $this->height; $i++){
					$z = $this->base->getFloorZ() + $i;
					$centre = new Vector3($this->base->getFloorX(), $this->base->getFloorY(), $z);
					for($x = $this->base->getFloorX() - $this->radius; $x <= $this->base->getFloorY() + $this->radius; $x++){
						for($y = $this->base->getFloorY() - $this->radius; $y <= $this->base->getFloorY() + $this->radius; $y++){
							$v = new Position($x, $y, $z, $this->base->getLevel());
							if($centre->distance($v) <= $this->radius){
								$out[] = $v;
							}
						}
					}
				}
				break;
		}
		return $out;
	}
	public function getBlockList(){
		$out = [];
		foreach($this->getPosList() as $pos){
			$out[] = $this->base->getLevel()->getBlock($pos);
		}
		return $out;
	}
	public function isInside(Vector3 $v){
		$out = true;
		switch($this->axis){
			case self::X:
				$out = ($out and $this->base->getX() <= $v->getX() and $v->getX() <= $this->base->getX() + $this->height);
				$out = ($out and $v->distance(new Vector3($v->getX(), $this->base->getY(), $this->base->getZ())) <= $this->radius);
				break;
			case self::Y:
				$out = ($out and $this->base->getY() <= $v->getY() and $v->getY() <= $this->base->getY() + $this->height);
				$out = ($out and $v->distance(new Vector3($this->base->getX(), $v->getY(), $this->base->getZ())) <= $this->radius);
				break;
			case self::Z:
				$out = ($out and $this->base->getZ() <= $v->getZ() and $v->getZ() <= $this->base->getZ() + $this->height);
				$out = ($out and $v->distance(new Vector3($this->base->getX(), $this->base->getY(), $v->getZ())) <= $this->radius);
				break;
		}
		if($v instanceof Position){
			$out = ($out and $v->getLevel()->getName() === $this->base->getLevel()->getName());
		}
		return $out;
	}
	public function __toString(){
		return "a cylinder of axis ".self::axisToStr($this->axis)." based at ".Main::posToStr($this->base)." with {$this->height} blocks long";
	}
	public function axisToStr($axis){
		if($axis === self::X){
			return "X";
		}
		if($axis === self::Y){
			return "Y";
		}
		return "Z";
	}
	public static function getVector($yaw, $pitch){
		$oldYawRef = $yaw;
		if($pitch > 45){ // >= or > ?
			return "y-";
		}
		if($pitch < -45){
			return "y+";
		}
		$yaw += 45;
		$yaw %= 360;
		$yaw = (int) ($yaw / 90);
		switch($yaw){
			case 0:
				return "z+";
			case 1:
				return "x-";
			case 2:
				return "z-";
			case 3:
				return "x+";
		}
		trigger_error("Yaw could not be parsed correctly as a vector in WorldEditArt [pemapmodder\\worldeditart\\utils\\spaces\\CylinderSpace::getVector($oldYawRef, $pitch)", E_USER_WARNING);
		return false;
	}
}
