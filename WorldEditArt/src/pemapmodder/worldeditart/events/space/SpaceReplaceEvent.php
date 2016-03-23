<?php

namespace pemapmodder\worldeditart\events\space;

use pemapmodder\worldeditart\utils\spaces\BlockList;
use pemapmodder\worldeditart\utils\spaces\Space;
use pemapmodder\worldeditart\WorldEditArt;
use pocketmine\Player;

class SpaceReplaceEvent extends SpaceEvent{
	public static $handlerList = null;
	/** @var \pocketmine\block\Block[] */
	private $from;
	/** @var BlockList */
	private $to;

	public function __construct(WorldEditArt $main, Space $space, Player $player, array $from, BlockList $to){
		parent::__construct($main, $space, $player);
		$this->from = $from;
		$this->to = $to;
	}

	/**
	 * @return \pocketmine\block\Block[]
	 */
	public function &getFrom(){
		return $this->from;
	}

	/**
	 * @param \pocketmine\block\Block[] $from
	 */
	public function setFrom($from){
		$this->from = $from;
	}

	/**
	 * @return \pemapmodder\worldeditart\utils\spaces\BlockList
	 */
	public function getTo(){
		return $this->to;
	}

	/**
	 * @param \pemapmodder\worldeditart\utils\spaces\BlockList $to
	 */
	public function setTo($to){
		$this->to = $to;
	}
}
