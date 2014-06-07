<?php

namespace pemapmodder\worldeditart\utils\spaces;

use pocketmine\block\Air;
use pocketmine\block\Block;
use pocketmine\level\Position;
use pocketmine\math\Vector3;

abstract class Space{
	/**
	 * @return \pocketmine\level\Position
	 */
	public abstract function getPosList();
	/**
	 * @return Block[]
	 */
	public abstract function getBlockList();
	/**
	 * @param Block $block
	 * @return int
	 */
	public function setBlocks(Block $block){
		$block = clone $block;
		$cnt = 0;
		foreach($this->getBlockList() as $b){
			if($b->getID() !== $block->getID() or $b->getDamage() !== $block->getDamage()){
				$b->getLevel()->setBlock($b, $block, false, false, true);
				$cnt++;
			}
		}
		return $cnt;
	}
	public function clear(){
		$this->setBlocks(new Air);
	}
	/**
	 * @param Block $orig
	 * @param Block $new
	 * @param bool $checkMeta
	 * @return int
	 */
	public function replaceBlocks(Block $orig, Block $new, $checkMeta = true){
		$orig = clone $orig;
		$new = clone $new;
		$cnt = 0;
		foreach($this->getBlockList() as $b){
			if($b->getID() === $orig->getID() and ($checkMeta === false or $b->getDamage() === $orig->getDamage())){
				$b->getLevel()->setBlock($b, $new, false, false, true);
				$cnt++;
			}
		}
		return $cnt;
	}
	public abstract function isInside(Vector3 $v);
	/**
	 * @param Vector3 $v0
	 * @param Vector3 $v1
	 */
	protected final function equals(Vector3 $v0, Vector3 $v1){
		$out = true;
		$out = ($out and $v0->getX() === $v1->getX());
		$out = ($out and $v0->getY() === $v1->getY());
		$out = ($out and $v0->getZ() === $v1->getZ());
		if($v0 instanceof Position and $v1 instanceof Position){
			$out = ($out and $v0->getLevel()->getName() === $v1->getLevel()->getName());
		}
	}
}
