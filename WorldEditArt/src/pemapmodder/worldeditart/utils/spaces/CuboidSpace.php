<?php

namespace pemapmodder\worldeditart\utils\spaces;

use pemapmodder\worldeditart\Main;
use pocketmine\level\Position;
use pocketmine\math\Vector3;

class CuboidSpace extends Space{
	/** @var Position  */
	protected $raw0, $raw1;
	/** @var Position */
	protected $baked0, $baked1;
	public static function createFromPos_Rot(Position $pos, $yaw, $pitch){
		$y = -sin(deg2rad($pitch));
		$xz = cos(deg2rad($pitch));
		$x = -$xz * sin(deg2rad($yaw));
		$z = $xz * cos(deg2rad($yaw));
		// CREDIT partially copied from PocketMine Entity.php source because I am too lazy to do those trigo :P
		$newPos = $pos->add($x, $y, $z);
		return new CuboidSpace($pos, $newPos);
	}
	public function __construct(Position $a, Vector3 $b){
		$this->raw0 = $a;
		if(!($b instanceof Position)){
			$b = new Position($b->getX(), $b->getY(), $b->getZ(), $a->getLevel());
		}
		$this->raw1 = $b;
		if($a->getLevel()->getName() !== $b->getLevel()->getName()){
			trigger_error("Positions of different levels (\"".$a->getLevel()->getName()."\" and \"".$b->getLevel()->getName()."\" passed to constructor of ".get_class($this), E_USER_WARNING);
		}
		$this->bake();
		parent::__construct(); // call this at the last because it has to bake before acquiring
	}
	public function set0(Vector3 $v){
		if($v instanceof Position and $v->getLevel()->getName() !== $this->baked0->getLevel()->getName()){
			trigger_error("Trying to set CuboidSpace to different level by CuboidSpace::set0()", E_USER_ERROR);
			return;
		}
		$this->raw0 = new Position($v->x, $v->y, $v->z, $this->baked0->getLevel());
		$this->bake();
	}
	public function set1(Vector3 $v){
		if($v instanceof Position and $v->getLevel()->getName() !== $this->baked0->getLevel()->getName()){
			trigger_error("Trying to set CuboidSpace to different level by CuboidSpace::set1()", E_USER_ERROR);
			return;
		}
		$this->raw1 = new Position($v->x, $v->y, $v->z, $this->baked0->getLevel());
		$this->bake();
	}
	public function getRaw0(){
		return $this->raw0;
	}
	public function getRaw1(){
		return $this->raw1;
	}
	public function bake(){
		if($this->raw0->getLevel()->getName() !== $this->raw1->getLevel()->getName()){
			trigger_error("Positions of different levels (\"".$this->raw0->getLevel()->getName()."\" and \"".$this->raw1->getLevel()->getName()."\" passed to constructor of ".get_class($this), E_USER_WARNING);
		}
		$this->baked0 = new Position(
			min($this->raw0->getFloorX(), $this->raw1->getFloorX()),
			min($this->raw0->getFloorY(), $this->raw1->getFloorY()),
			min($this->raw0->getFloorZ(), $this->raw1->getFloorZ()),
			$this->raw0->getLevel()
		);
		$this->baked1 = new Position(
			max($this->raw0->getFloorX(), $this->raw1->getFloorX()),
			max($this->raw0->getFloorY(), $this->raw1->getFloorY()),
			max($this->raw0->getFloorZ(), $this->raw1->getFloorZ()),
			$this->raw1->getLevel()
		);
		$maxHeight = 127;
		if(defined($path = "pemapmodder\\worldeditart\\MAX_WORLD_HEIGHT")){
			$maxHeight = constant($path);
		}
		if($this->baked1->getFloorY() > $maxHeight or $this->baked0->getFloorY() < 0){
			throw new SelectionExceedWorldException("CuboidSpace");
		}
	}
	public function get0(){
		$this->bake();
		return $this->baked0;
	}
	public function get1(){
		$this->bake();
		return $this->baked1;
	}
	public function getPosList(){
		$pos = [];
		for($x = $this->baked0->getX(); $x <= $this->baked1->getX(); $x++){
			for($y = $this->baked0->getY(); $y <= $this->baked1->getY(); $y++){
				for($z = $this->baked0->getZ(); $z <= $this->baked1->getZ(); $z++){
					$pos[] = new Position($x, $y, $z, $this->baked0->getLevel());
				}
			}
		}
		return $pos;
	}
	public function getBlockList(){
		$blocks = [];
		for($x = $this->baked0->getX(); $x <= $this->baked1->getX(); $x++){
			for($y = $this->baked0->getY(); $y <= $this->baked1->getY(); $y++){
				for($z = $this->baked0->getZ(); $z <= $this->baked1->getZ(); $z++){
					$blocks[] = $this->baked0->getLevel()->getBlock(new Vector3($x, $y, $z));
				}
			}
		}
		return $blocks;
	}
	public function getMarginPosList(){
		$out = [];
		for($x = $this->baked0->x; $x <= $this->baked1->x; $x++){
			for($y = $this->baked0->y; $y <= $this->baked1->y; $y++){
				$out[] = new Position($x, $y, $this->baked0->z, $this->raw0->getLevel());
				$out[] = new Position($x, $y, $this->baked1->z, $this->raw0->getLevel());
			}
		}
		for($y = $this->baked0->y; $y <= $this->baked1->y; $y++){
			for($z = $this->baked0->z + 1; $z < $this->baked1->z; $z++){
				$out[] = new Position($this->baked0->x, $y, $z, $this->raw0->getLevel());
				$out[] = new Position($this->baked1->x, $y, $z, $this->raw0->getLevel());
			}
		}
		for($x = $this->baked0->x + 1; $x < $this->baked1->x; $x++){
			for($z = $this->baked0->z + 1; $z < $this->baked1->z; $z++){
				$out[] = new Position($x, $this->baked0->y, $z, $this->raw0->getLevel());
				$out[] = new Position($x, $this->baked1->y, $z, $this->raw0->getLevel());
			}
		}
		return $out;
	}
	public function getMarginBlockList(){
		$level = $this->raw0->getLevel();
		$out = [];
		for($x = $this->baked0->x; $x <= $this->baked1->x; $x++){
			for($y = $this->baked0->y; $y <= $this->baked1->y; $y++){
				$out[] = $level->getBlock(new Vector3($x, $y, $this->baked0->z));
				$out[] = $level->getBlock(new Vector3($x, $y, $this->baked1->z));
			}
		}
		for($y = $this->baked0->y; $y <= $this->baked1->y; $y++){
			for($z = $this->baked0->z + 1; $z < $this->baked1->z; $z++){
				$out[] = $level->getBlock(new Vector3($this->baked0->x, $y, $z));
				$out[] = $level->getBlock(new Vector3($this->baked1->x, $y, $z));
			}
		}
		for($x = $this->baked0->x + 1; $x < $this->baked1->x; $x++){
			for($z = $this->baked0->z + 1; $z < $this->baked1->z; $z++){
				$out[] = $level->getBlock(new Vector3($x, $this->baked0->y, $z));
				$out[] = $level->getBlock(new Vector3($x, $this->baked1->y, $z));
			}
		}
		return $out;
	}
	public function isInside(Vector3 $v){
		$out = true;
		$out = ($out and $this->baked0->getFloorX() <= $v->getX() and $v->getX() <= $this->baked1->getFloorX());
		$out = ($out and $this->baked0->getFloorY() <= $v->getY() and $v->getY() <= $this->baked1->getFloorY());
		$out = ($out and $this->baked0->getFloorZ() <= $v->getZ() and $v->getZ() <= $this->baked1->getFloorZ());
		if($v instanceof Position){
			$out = ($out and $this->baked0->getLevel()->getName() === $v->getLevel()->getName());
		}
		return $out;
	}
	public function getLevel(){
		return $this->baked0->getLevel();
	}
	public function acquire(){
		$this->baked0->level->acquire();
	}
	public function __toString(){
		return "a cuboid from ".Main::v3ToStr($this->raw0)." to ".Main::posToStr($this->raw1);
	}
}
