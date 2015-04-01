<?php

namespace authtools;

use pocketmine\Player;

class AuthToolsSession{
	const NORM = 0;
	const AUTH = 1;
	const NEW_PW = 2;
	/** @var Player */
	public $player;
	public $chatState = self::NORM;
	/** @var int */
	public $chatSubstate = 0;
	public $tmpPwHash = null;

	public function __construct(Player $player){
		$this->player = $player;
	}
	public function id(){
		return $this->player->getId();
	}
	public function reset(){
		$this->chatState = self::NORM;
		$this->chatSubstate = 0;
		$this->tmpPwHash = null;
	}
}
