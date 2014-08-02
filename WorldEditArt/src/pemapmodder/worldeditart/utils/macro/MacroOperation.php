<?php

namespace pemapmodder\worldeditart\utils\macro;

use pocketmine\block\Block;
use pocketmine\level\Position;
use pocketmine\math\Vector3;
use pocketmine\nbt\tag;

class MacroOperation{
	const TYPE_WAIT = true;
	const TYPE_OPERATE = false;
	public static function fromTag(tag\Compound $compound){
		$type = $compound["type"];
		if($type === 1){
			return new MacroOperation($compound["delta"]);
		}
		else{
			$vectors = $compound["vectors"];
			return new MacroOperation(new Vector3($vectors[0], $vectors[1], $vectors[2]), Block::get($compound["blockID"], $compound["blockDamage"]));
		}
	}
	/**
	 * @param Vector3|int $pos
	 * @param Block|null $block
	 * @throws \InvalidArgumentException
	 */
	public function __construct($pos, $block = null){
		if(is_int($pos)){
			if($pos >= 0x100000000){
				throw new \InvalidArgumentException("Macro wait time cannot exceed ".(0x100000000 / 20)." seconds.");
			}
		}
		$this->delta = $pos;
		$this->block = $block;
	}
	public function toTag(){
		$tag = new tag\Compound;
		$tag["type"] = new tag\Byte("", is_int($this->delta) ? 1:0);
		if(is_int($this->delta)){
			$tag["delta"] = new tag\Int("", $this->delta);
		}
		else{
			$tag["vectors"] = new tag\Enum("", [
				new tag\Long("", $this->delta->getFloorX()),
				new tag\Short("", $this->delta->getFloorY()),
				new tag\Long("", $this->delta->getFloorZ())
			]);
			$tag["blockID"] = new tag\Byte("", $this->block->getID());
			$tag["blockDamage"] = new tag\Byte("", $this->block->getDamage());
		}
		return $tag;
	}
	public function operate(Position $anchor){
		if(is_int($this->delta)){
			throw new \BadMethodCallException("MacroOperation is of type TRUE (wait) not FALSE (operate) thus cannot be operated");
		}
		$anchor->getLevel()->setBlock($anchor->add($this->delta), $this->block, true, true); // update
	}
	public function getLength(){
		if(!is_int($this->delta)){
			throw new \BadMethodCallException("MacroOperation is of type FALSE (operate) not TRUE (wait) thus no length can be resolved");
		}
		return $this->delta;
	}
	public function getType(){
		return is_int($this->delta);
	}
}
