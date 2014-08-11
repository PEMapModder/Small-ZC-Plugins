<?php

namespace pemapmodder\worldeditart\events;

use pemapmodder\worldeditart\Main;
use pocketmine\level\Position;
use pocketmine\Player;

class AnchorChangeEvent extends CancellableWorldEditArtEvent{
	public static $handlerList = null;
	/** @var Player */
	private $player;
	/** @var Position */
	private $anchor;
	public function __construct(Main $main, Player $player, Position $anchor){
		parent::__construct($main);
		$this->player = $player;
		$this->anchor = clone $anchor;
	}
	/**
	 * @return Player
	 */
	public function getPlayer(){
		return $this->player;
	}
	/**
	 * @return Position
	 */
	public function getAnchor(){
		return $this->anchor;
	}
	/**
	 * @param Position $anchor
	 */
	public function setAnchor($anchor){
		$this->anchor = $anchor;
	}
}
