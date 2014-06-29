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
		$id = ord(substr($string, 0, 1));
		$damage = ord(substr($string, 1, 1));
		$x = Binary::readLong(substr($string, 2, 8), true);
		$y = Binary::readShort(substr($string, 10, 2), true);
		$z = Binary::readLong(substr($string, 12, 8), true);
		return new MacroOperation(new Vector3($x, $y, $z), Block::get($id, $damage));
	}
	/**
	 * @param Vector3 $pos
	 * @param Block $block
	 */
	public function __construct(Vector3 $pos, Block $block){
		$this->delta = $pos;
		$this->block = $block;
	}
	public function __toString(){
		$output = "";
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
