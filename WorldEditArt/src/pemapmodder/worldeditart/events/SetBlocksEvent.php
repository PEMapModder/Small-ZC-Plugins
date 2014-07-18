<?php

namespace pemapmodder\worldeditart\events;

use pemapmodder\worldeditart\utils\spaces\Space;
use pocketmine\block\Block;
use pocketmine\Player;

class SetBlocksEvent extends SpaceEditEvent{
	public static $handlerList = null;
	/** @var Block */
	private $block;
	/** @var float|bool */
	private $percentage;
	public function __construct(Space $space, Block $block, Player $player, $percentage){
		$this->player = $player;
		$this->space = $space;
		$this->block = $block;
		$this->percentage = $percentage;
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
	/**
	 * @return bool|float
	 */
	public function getPercentage(){
		return $this->percentage;
	}
	/**
	 * @param bool|float $percentage
	 */
	public function setPercentage($percentage){
		$this->percentage = $percentage;
	}
}
