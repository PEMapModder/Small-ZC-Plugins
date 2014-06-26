<?php

namespace pemapmodder\worldeditart\events;

use pemapmodder\worldeditart\utils\spaces\Space;
use pocketmine\block\Block;
use pocketmine\Player;

class TestSelectionEvent extends SpaceEditEvent{
	/**
	 * @var Block
	 */
	private $block;
	/**
	 * @var int
	 */
	private $length;
	public function __construct(Space $space, Block $block, $length, Player $player){
		$this->space = $space;
		$this->block = $block;
		$this->length = $length;
		$this->player = $player;
	}
	/**
	 * @return \pocketmine\block\Block
	 */
	public function getBlock(){
		return $this->block;
	}
	/**
	 * @param \pocketmine\block\Block $block
	 */
	public function setBlock($block){
		$this->block = $block;
	}
	/**
	 * @return int
	 */
	public function getLength(){
		return $this->length;
	}
	/**
	 * @param int $length
	 */
	public function setLength($length){
		$this->length = $length;
	}
}
