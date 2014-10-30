<?php

namespace chatchannels;

use pocketmine\Player;

class PlayerSubscriber implements ChannelSubscriber{
	/** @var Player */
	private $player;
	/** @var int */
	public $level;
	/** @var bool */
	public $muted;
	public function __construct(Player $player){
		$this->player = $player;
	}
	public function getID(){
		return "player/" . strtolower($this->player->getName());
	}
	public function getDisplayName(){
		return $this->player->getDisplayName();
	}
	public function getSubscribingLevel(){
		return $this->level;
	}
	public function sendMessage($message, Channel $channel){
		$this->player->sendMessage("<#$channel> $message");
	}
	public function isMuted(){
		return $this->muted;
	}
	public function release(){
		$this->player = null;
	}
}
