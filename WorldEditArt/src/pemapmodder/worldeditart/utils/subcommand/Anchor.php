<?php

namespace pemapmodder\worldeditart\utils\subcommand;

use pemapmodder\worldeditart\Main;
use pocketmine\level\Position;
use pocketmine\Player;

class Anchor extends Subcommand{
	public function getName(){
		return "anchor";
	}
	public function getDescription(){
		return "Manage your anchor";
	}
	public function getUsage(){
		return "[me|sel]";
	}
	public function onRun(array $args, Player $player){
		if(!isset($args[0])){ // view
			if(!$player->hasPermission("wea.anchor.view")){
				return self::NO_PERM;
			}
			$anchor = $this->getMain()->getAnchor($player);
			if(!($anchor instanceof Position)){
				return self::NO_ANCHOR;
			}
			return "Your anchor has been set to ".Main::posToStr($anchor);
		}
		switch($args[0]){
			case "me":
			case "here":
				if(!$player->hasPermission("wea.anchor.set.here")){
					return self::NO_PERM;
				}
				$this->getMain()->setAnchor($player, $player->getPosition());
				return "Your anchor has been set to ".Main::posToStr($player);
			case "sel":
				if(!$player->hasPermission("wea.anchor.set.sel")){
					return self::NO_PERM;
				}
				if(!($sel = $this->getMain()->getSelectedPoint($player)) instanceof Position){
					return self::NO_SELECTED_POINT;
				}
				$this->getMain()->setAnchor($player, $sel);
				return "Your anchor has been set to ".Main::posToStr($sel);
			default:
				return self::WRONG_USE;
		}
	}
	public function checkPermission(Player $player){
		return $player->hasPermission("wea.anchor.view") or $player->hasPermission("wea.anchor.set.here") or $player->hasPermission("wea.anchor.set.sel");
	}
}
