<?php

namespace pemapmodder\worldeditart\events;

use pemapmodder\worldeditart\utils\spaces\Space;
use pocketmine\event\Cancellable;
use pocketmine\event\player\PlayerEvent;
use pocketmine\Player;

abstract class SpaceEditEvent extends PlayerEvent implements Cancellable{
	/**
	 * @var \pemapmodder\worldeditart\utils\spaces\Space
	 */
	protected $space;
	protected $cancelMsg = "rejected by another plugin (unknown reason)";
	public function __construct(Player $player, Space $space){
		$this->player = $player;
		$this->space = $space;
	}
	public function getCancelMessage(){
		return $this->cancelMsg;
	}
	public function setCancelMessage($msg){
		$this->cancelMsg = $msg;
	}
	/**
	 * @return \pemapmodder\worldeditart\utils\spaces\Space
	 */
	public function getSpace(){
		return $this->space;
	}
}
