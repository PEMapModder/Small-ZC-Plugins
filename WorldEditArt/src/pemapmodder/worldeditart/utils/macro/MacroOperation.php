<?php

namespace pemapmodder\worldeditart\utils\macro;

use pocketmine\block\Block;
use pocketmine\level\Position;
use pocketmine\math\Vector3;
use pocketmine\utils\Binary;

class MacroOperation{
	/** @var Vector3 */
	private $delta;
	/** @var Block $block */
	private $block;
	public static function parse($string){
		$type = substr($string, 0, 1);
		if($type === "\x01"){
			return Binary::readInt(substr($string, 1));
		}
		$id = ord(substr($string, 1, 1));
		$damage = ord(substr($string, 2, 1));
		$x = Binary::readLong(substr($string, 3, 8), true);
		$y = Binary::readShort(substr($string, 11, 2), true);
		$z = Binary::readLong(substr($string, 13, 8), true);
		return new MacroOperation(new Vector3($x, $y, $z), Block::get($id, $damage));
	}
	/**
	 * @param Vector3|int $pos
	 * @param Block|null $block
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
	public function __toString(){
		if(is_int($this->delta)){
			return "\x01".Binary::writeInt($this->delta);
		}
		$output = "\x00";
		$output .= chr($this->block->getID());
		$output .= chr($this->block->getDamage());
		$output .= Binary::writeLong($this->delta->getFloorX());
		$output .= Binary::writeShort($this->delta->getFloorY());
		$output .= Binary::writeLong($this->delta->getFloorZ());
		return $output;
	}
	public function operate(Position $pos){
		$pos->getLevel()->setBlock($pos->add($this->delta), $this->block, true, true); // true
	}
}
