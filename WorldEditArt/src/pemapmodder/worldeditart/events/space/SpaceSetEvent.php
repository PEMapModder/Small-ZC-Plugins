<?php

namespace pemapmodder\worldeditart\events\space;

use pemapmodder\worldeditart\utils\spaces\BlockList;
use pemapmodder\worldeditart\utils\spaces\Space;
use pemapmodder\worldeditart\WorldEditArt;
use pocketmine\Player;

class SpaceSetEvent extends SpaceEvent{
	public static $handlerList = null;
	/** @var BlockList */
	private $blocks;

	public function __construct(WorldEditArt $main, Space $space, Player $player, BlockList $blocks){
		parent::__construct($main, $space, $player);
		$this->blocks = $blocks;
	}

	/**
	 * @return BlockList
	 */
	public function getBlocks(){
		return $this->blocks;
	}

	/**
	 * @param BlockList $blocks
	 */
	public function setBlocks($blocks){
		$this->blocks = $blocks;
	}
}
