<?php

namespace pemapmodder\worldeditart\utils\spaces;

use pocketmine\level\Position;
use pocketmine\math\Vector3;

class CuboidSpace extends Space{
	/** @var Position  */
	protected $raw0, $raw1;
	/** @var Position */
	protected $baked0, $baked1;
	public function __construct(Position $a, Position $b){
		$this->raw0 = $a;
		$this->raw1 = $b;
		if($a->getLevel()->getName() !== $b->getLevel()->getName()){
			trigger_error("Positions of different levels (\"".$a->getLevel()->getName()."\" and \"".$b->getLevel()->getName()."\" passed to constructor of ".get_class($this), E_USER_WARNING);
		}
		$this->bake();
	}
	public function bake(){
		$this->baked0 = new Position(
			min($this->raw0->getX(), $this->raw1->getX()),
			min($this->raw0->getY(), $this->raw1->getY()),
			min($this->raw0->getZ(), $this->raw1->getZ()),
			$this->raw0->getLevel()
		);
		$this->baked1 = new Position(
			max($this->raw0->getX(), $this->raw1->getX()),
			max($this->raw0->getY(), $this->raw1->getY()),
			max($this->raw0->getZ(), $this->raw1->getZ()),
			$this->raw1->getLevel()
		);
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
	public function isInside(Vector3 $v){

	}
}
