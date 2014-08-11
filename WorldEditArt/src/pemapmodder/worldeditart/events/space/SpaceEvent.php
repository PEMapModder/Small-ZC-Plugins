<?php

namespace pemapmodder\worldeditart\events\space;

use pemapmodder\worldeditart\events\CancellableWorldEditArtEvent;
use pemapmodder\worldeditart\Main;
use pemapmodder\worldeditart\utils\spaces\Space;
use pocketmine\Player;

abstract class SpaceEvent extends CancellableWorldEditArtEvent{
	protected $space;
	protected $player;
	protected function __construct(Main $main, Space $space, Player $player){
		parent::__construct($main);
		$this->space = $space;
		$this->player = $player;
	}
	/**
	 * @return Space
	 */
	public function getSpace(){
		return $this->space;
	}
	/**
	 * @return Player
	 */
	public function getPlayer(){
		return $this->player;
	}
}
