<?php

namespace pemapmodder\worldeditart\events;

use pemapmodder\worldeditart\utils\spaces\Space;
use pocketmine\block\Block;
use pocketmine\Player;

class SetBlocksEvent extends SpaceEditEvent{
	public static $handlerList = null;
	/**
	 * @var Block $block
	 */
	private $block;
	public function __construct(Space $space, Block $block, Player $player){
		$this->player = $player;
		$this->space = $space;
		$this->block = $block;
	}
	/**
	 * @return Block
	 */
	public function getBlock(){
		return $this->block;
	}
	/**
	 * @param Block $block
	 */
	public function setBlock($block){
		$this->block = $block;
	}
}
