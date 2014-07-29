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
	 * @return Position[]
	 */
	public abstract function getPosList();
	/**
	 * @return Block[]
	 */
	public function getBlockList(){
		$out = [];
		foreach($this->getPosList() as $pos){
			$out[] = $pos->getLevel()->getBlock($pos);
		}
		return $out;
	}
	/**
	 * @return Position[]
	 */
	public function getMarginPosList(){
		$out = [];
		foreach($this->getPosList() as $pos){
			$ok = false;
			for($i = 0; $i < 6; $i++){
				if(!$this->isInside($pos->getSide($i))){
					// add it as long as it is visible from the outside (an adjacent block is not in the space)
					$ok = true;
					break;
				}
			}
			if($ok){
				$out[] = $pos;
			}
		}
		return $out;
	}
	/**
	 * @return Block[]
	 */
	public function getMarginBlockList(){
		$out = [];
		foreach($this->getMarginPosList() as $pos){
			$out[] = $pos->getLevel()->getBlock($pos);
		}
		return $out;
	}
	/**
	 * @param Block $block
	 * @param Player|bool $test
	 * @param bool $update
	 * @return int
	 */
	public function setBlocks(Block $block, $test = false, $update = true){
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
		if($update and $test === false){
			$this->updateAround();
		}
		return $cnt;
	}
	/**
	 * @param Player|bool $test
	 * @param bool $update
	 */
	public function clear($test = false, $update = true){
		$this->setBlocks(new Air, $test, $update);
	}
	/**
	 * Note: This method doesn't support /w test since it is random.
	 * @param BlockList $blocks
	 * @return int the number of bocks replaced
	 */
	public function randomPlaces(BlockList $blocks){
		$cnt = 0;
		foreach($this->getPosList() as $pos){
			$pos->getLevel()->setBlock($pos, $blocks->getRandom(), true, false);
			$cnt++;
		}
		$this->updateAround();
		return $cnt;
	}
	/**
	 * @param Block $orig
	 * @param Block $new
	 * @param bool $checkMeta
	 * @param Player|bool $test
	 * @param bool $update
	 * @return int
	 */
	public function replaceBlocks(Block $orig, Block $new, $checkMeta = true, $test = false, $update = true){
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
		if($test === false and $update){
			$this->updateAround();
		}
		return $cnt;
	}
	/**
	 * @param Block $from
	 * @param BlockList $to
	 * @param bool $checkMeta
	 * @param bool $update
	 * @return int
	 */
	public function randomReplaces(Block $from, BlockList $to, $checkMeta = true, $update = true){
		$cnt = 0;
		foreach($this->getPosList() as $pos){
			$level = $pos->getLevel();
			$block = $level->getBlock($pos);
			if($block->getID() === $from->getID() and (!$checkMeta or $block->getDamage() === $from->getDamage())){
				$level->setBlock($pos, $to->getRandom(), true, false);
				$cnt++;
			}
		}
		if($update){
			$this->updateAround();
		}
		return $cnt;
	}
	/**
	 * @return \pocketmine\level\Level
	 */
	public abstract function getLevel();
	public function updateAround(){
		foreach($this->getMarginPosList() as $pos){
			$this->getLevel()->updateAround($pos);
		}
	}
	public function undoLastTest(){
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
