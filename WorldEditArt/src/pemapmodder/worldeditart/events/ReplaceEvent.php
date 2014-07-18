<?php

namespace pemapmodder\worldeditart\events;

use pemapmodder\worldeditart\utils\spaces\Space;
use pocketmine\block\Block;
use pocketmine\Player;

class ReplaceEvent extends SpaceEditEvent{
	/**
	 * @var Block
	 */
	private $from;
	/** @var Block */
	private $to;
	/** @var float|bool */
	private $percentage;
	public function __construct(Space $space, Block $from, Block $to, Player $player, $percentage){
		parent::__construct($player, $space);
		$this->from = $from;
		$this->to = $to;
		$this->percentage = $percentage;
	}
	/**
	 * @return float
	 */
	public function getPercentage(){
		return $this->percentage;
	}
	/**
	 * @param float $percentage
	 */
	public function setPercentage($percentage){
		$this->percentage = $percentage;
	}
	/**
	 * @return Block
	 */
	public function getTo(){
		return $this->to;
	}
	/**
	 * @return Block
	 */
	public function getFrom(){
		return $this->from;
	}
	/**
	 * @param Block $to
	 */
	public function setTo($to){
		$this->to = $to;
	}
	/**
	 * @param Block $from
	 */
	public function setFrom($from){
		$this->from = $from;
	}
}
