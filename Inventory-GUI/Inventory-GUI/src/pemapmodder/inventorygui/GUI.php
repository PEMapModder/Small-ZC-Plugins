<?php

namespace pemapmodder\inventorygui;

use pocketmine\Player;

interface GUI{
	/**
	 * @return int
	 */
	public function getID();
	/**
	 * @return int
	 */
	public function getDamage();
	/**
	 * @return number
	 */
	public function getPriority();
	/**
	 * @param Player $player
	 * @return mixed
	 */
	public function onActivation(Player $player);
}
