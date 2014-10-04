<?php

namespace pemapmodder\nailedkeyboard;

use pocketmine\event\player\PlayerCommandPreprocessEvent;
use pocketmine\Player;

class PlayerCommandPreprocessEvent_sub extends PlayerCommandPreprocessEvent{
	private $plugin;
	public function __construct(Player $player, $message, NailedKeyboard $plugin){
		parent::__construct($player, $message);
		$this->plugin = $plugin;
	}
	/**
	 * @return NailedKeyboard
	 */
	public function getPlugin(){
		return $this->plugin;
	}
}
