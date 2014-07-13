<?php

namespace pemapmodder\worldeditart\events;

use pocketmine\event\Cancellable;
use pocketmine\event\player\PlayerEvent;
use pocketmine\Player;

class PasteEvent extends PlayerEvent implements Cancellable{
	public static $handlerList = null;
	/** @var mixed[] */
	private $clip;
	/** @var bool */
	private $isGlobal;
	private $cancelMessage = "Pasting has been cancelled by another plugin!";
	/**
	 * @param Player $player
	 * @param mixed[] $clip
	 * @param bool $isGlobal
	 */
	public function __construct(Player $player, array $clip, $isGlobal){
		$this->player = $player;
		$this->clip = $clip;
		$this->isGlobal = $isGlobal;
	}
	/**
	 * @return boolean
	 */
	public function getIsGlobal(){
		return $this->isGlobal;
	}
	/**
	 * @return mixed
	 */
	public function getClip(){
		return $this->clip;
	}
	/**
	 * @param mixed $clip
	 */
	public function setClip($clip){
		$this->clip = $clip;
	}
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
}
