<?php

namespace pemapmodder\worldeditart\utils\spaces;

use pocketmine\block\Air;
use pocketmine\block\Block;
use pocketmine\level\Position;
use pocketmine\math\Vector3;
use pocketmine\network\protocol\UpdateBlockPacket;
use pocketmine\Player;

abstract class Space implements \Countable{
	/** @var Block[] */
	protected $undoMap = [];

	public function __construct(){
	}

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
	 * @param Block       $block
	 * @param Player|bool $test
	 * @param bool        $update
	 *
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
				}else{
					$b->getLevel()->setBlock($b, $block, false, false); // w** shoghicp
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
	 * @param bool        $update
	 */
	public function clear($test = false, $update = true){
		$this->setBlocks(new Air, $test, $update);
	}

	/**
	 * Note: This method doesn't support /w test since it is random.
	 *
	 * @param BlockList $blocks
	 * @param bool      $update
	 *
	 * @return int the number of bocks replaced
	 */
	public function randomPlaces(BlockList $blocks, $update = true){
		$cnt = 0;
		foreach($this->getPosList() as $pos){
			$pos->getLevel()->setBlock($pos, $blocks->getRandom(), false, false);
			$cnt++;
		}
		if($update){
			$this->updateAround();
		}
		return $cnt;
	}

	/**
	 * @param Block|Block[] $oorig
	 * @param Block         $new
	 * @param bool          $checkMeta
	 * @param Player|bool   $test
	 * @param bool          $update
	 *
	 * @return int
	 */
	public function replaceBlocks($oorig, Block $new, $checkMeta = true, $test = false, $update = true){
		/** @var Block[] $origs */
		$origs = [];
		foreach(((array) $oorig) as $b){
			$origs[] = clone $b;
		}
		$new = clone $new;
		$cnt = 0;
		foreach($this->getBlockList() as $b){
			$valid = false;
			foreach($origs as $orig){
				if($b->getID() === $orig->getID() and ($checkMeta === false or $b->getDamage() === $orig->getDamage())){
					$valid = true;
					break;
				}
			}
			if($valid){
				if($test instanceof Player){
					$pk = new UpdateBlockPacket;
					$pk->block = $new->getID();
					$pk->meta = $new->getDamage();
					$pk->x = $b->x;
					$pk->y = $b->y;
					$pk->z = $b->z;
					$test->dataPacket($pk);
				}else{
					$b->getLevel()->setBlock($b, $new, false, false); // w** shoghicp
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
	 * @param Block|Block[] $ofroms
	 * @param BlockList     $to
	 * @param bool          $checkMeta
	 * @param bool          $update
	 *
	 * @return int
	 */
	public function randomReplaces($ofroms, BlockList $to, $checkMeta = true, $update = true){
		/** @var Block[] $froms */
		$froms = [];
		foreach(((array) $ofroms) as $from){
			$froms[] = clone $from;
		}
		$cnt = 0;
		foreach($this->getPosList() as $pos){
			$level = $pos->getLevel();
			$block = $level->getBlock($pos);
			$valid = false;
			foreach($froms as $from){
				if($block->getID() === $from->getID() and (!$checkMeta or $block->getDamage() === $from->getDamage())){
					$valid = true;
					break;
				}
			}
			if($valid){
				$level->setBlock($pos, $to->getRandom(), false, false);
				$cnt++;
			}
		}
		if($update){
			$this->updateAround();
		}
		return $cnt;
	}

	public function randomHollow(BlockList $blocks, $update = true){
		$cnt = 0;
		foreach($this->getMarginPosList() as $pos){
			$this->getLevel()->setBlock($pos, $blocks->getRandom(), false, $update);
			$cnt++;
		}
		return $cnt;
	}

	public function randomHollowReplace($froms, BlockList $list, $update = true){
		$cnt = 0;
		foreach($this->getMarginBlockList() as $b){
			$equal = false;
			foreach($froms as $from){
				if(self::equals($from, $b)){
					$equal = true;
					break;
				}
			}
			if($equal){
				$this->getLevel()->setBlock($b, $list->getRandom(), false, $update);
				$cnt++;
			}
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

	public abstract function isInside(Vector3 $v);

	/**
	 * @param Vector3 $v0
	 * @param Vector3 $v1
	 *
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

	public function count(){
		return count($this->getPosList());
	}

	public abstract function __toString();
}
