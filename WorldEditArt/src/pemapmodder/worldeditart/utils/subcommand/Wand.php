<?php

namespace pemapmodder\worldeditart\utils\subcommand;

use pocketmine\Player;

class Wand extends Subcommand{
	public function getName(){
		return "wand";
	}
	public function getDescription(){
		return "Set own's wand";
	}
	public function getUsage(){
		return "/w wand [cd|check-damage]";
	}
	public function checkPermission(Player $player){
		// TODO
	}
	public function onRun(array $args, Player $player){
		$item = $player->getInventory()->getItemInHand();
		$this->getMain()->setWand($player, $item->getID(), isset($args[0]) and ($args[0] === "cd" or $args[0] === "check-damage") ? $item->getDamage():true);
	}
}
