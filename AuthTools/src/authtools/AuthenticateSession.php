<?php

namespace authtools;

use pocketmine\Player;

class AuthenticateSession{
	/** @var action\Action */
	public $currentAction = null;
	/** @var Player */
	public $player;
	/** @var string */
	public $cachedHash = null;
	public $failureCnt = 0;

	public function __construct(Player $player){
		$this->player = $player;
	}
	public function close(){
		
	}
}
