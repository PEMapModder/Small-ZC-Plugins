<?php

namespace pemapmodder\worldeditart\utils\spaces;

use pocketmine\block\Air;
use pocketmine\block\Block;
use pocketmine\level\Position;
use pocketmine\math\Vector3;
use pocketmine\network\protocol\UpdateBlockPacket;
use pocketmine\Player;

abstract class Space{
	/** @var Block[] */
	protected $undoMap = [];
	/**
	 * @return \pocketmine\level\Position[]
	 */
	public abstract function getPosList();
	/**
	 * @return Block[]
	 */
	public abstract function getBlockList();
	/**
	 * @param Block $block
	 * @param Player|bool $test
	 * @return int
	 */
	public function setBlocks(Block $block, $test = false){
		$block = clone $block;
		$cnt = 0;
		foreach($this->getBlockList() as $b){
			if($b->getID() !== $block->getID() or $b->getDamage() !== $block->getDamage()){
				if($test instanceof Player){
					$pk = new UpdateBlockPacket;
					$pk->block = $block->getID();
					$pk->meta = $block->getDamage();
					$pk->x = $b->x;
					$pk->y = $b->y;
					$pk->z = $b->z;
					$test->dataPacket($pk);
				}
				else{
					$b->getLevel()->setBlock($b, $block, true, false); // w** shoghicp
				}
				$cnt++;
			}
		}
		return $cnt;
	}
	/**
	 * @param Player|bool $test
	 */
	public function clear($test = false){
		$this->setBlocks(new Air, $test);
	}
	/**
	 * @param Block $orig
	 * @param Block $new
	 * @param bool $checkMeta
	 * @param Player|bool $test
	 * @return int
	 */
	public function replaceBlocks(Block $orig, Block $new, $checkMeta = true, $test = false){
		$orig = clone $orig;
		$new = clone $new;
		$cnt = 0;
		if($test instanceof Player){
			$this->undoMap = []; // reset the undo map
		}
		foreach($this->getBlockList() as $b){
			if($b->getID() === $orig->getID() and ($checkMeta === false or $b->getDamage() === $orig->getDamage())){
				if($test instanceof Player){
					$pk = new UpdateBlockPacket;
					$pk->block = $new->getID();
					$pk->meta = $new->getDamage();
					$pk->x = $b->x;
					$pk->y = $b->y;
					$pk->z = $b->z;
					$test->dataPacket($pk);
					$this->undoMap[] = clone $b;
				}
				else{
					$b->getLevel()->setBlock($b, $new, true, false); // w** shoghicp
				}
				$cnt++;
			}
		}
		return $cnt;
	}
	public function undoLast(){
		foreach($this->undoMap as $block){
			$block->getLevel()->setBlock($block, $block, true, false);
		}
	}
	public abstract function isInside(Vector3 $v);
	/**
	 * @param Vector3 $v0
	 * @param Vector3 $v1
	 * @return bool
	 */
	protected final function equals(Vector3 $v0, Vector3 $v1){
		$out = true;
		$out = ($out and $v0->getX() === $v1->getX());
		$out = ($out and $v0->getY() === $v1->getY());
		$out = ($out and $v0->getZ() === $v1->getZ());
		if($v0 instanceof Position and $v1 instanceof Position){
			$out = ($out and $v0->getLevel()->getName() === $v1->getLevel()->getName());
		}
		return $out;
	}
	public abstract function __toString();
}
