<?php

namespace pemapmodder\worldeditart\events;

use pocketmine\event\Cancellable;
use pocketmine\Player;

abstract class CancellableWorldEditArtEvent extends WorldEditArtEvent implements Cancellable{
	protected $cancelMessage = "rejected by another plugin";
	/**
	 * @return string
	 */
	public function getCancelMessage(){
		return $this->cancelMessage;
	}
	/**
	 * @param string $cancelMessage
	 */
	public function setCancelMessage($cancelMessage){
		$this->cancelMessage = $cancelMessage;
	}
	public function sendCancelMessage(Player $player){
		if(is_string($this->cancelMessage)){
			$player->sendMessage($this->cancelMessage);
		}
	}
}
