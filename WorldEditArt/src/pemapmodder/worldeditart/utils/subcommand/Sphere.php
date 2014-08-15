<?php

namespace pemapmodder\worldeditart\utils\subcommand;

use pemapmodder\worldeditart\utils\spaces\SphereSpace;
use pocketmine\level\Position;
use pocketmine\Player;

class Sphere extends Subcommand{
	public function getName(){
		return "sphere";
	}
	public function getDescription(){
		return "Select a sphere";
	}
	public function getUsage(){
		return "<radius> [a|anchor]";
	}
	public function checkPermission(/** @noinspection PhpUnusedParameterInspection */
		Player $player){
		return true; // TODO
	}
	public function onRun(array $args, Player $player){
		if(!isset($args[0])){
			return false;
		}
		$radius = floatval(array_shift($args));
		$anchor = $player->getPosition();
		while(isset($args[0])){
			$arg = array_shift($args);
			switch($arg){
				case "a":
				case "anchor":
					$anchor = $this->getMain()->getAnchor($player);
					if(!($anchor instanceof Position)){
						return self::NO_ANCHOR;
					}
					break;
			}
		}
		$sel = new SphereSpace($anchor, $radius);
		$this->getMain()->setSelection($player, $sel);
		return "You have selected $sel.";
	}
	public function getAliases(){
		return ["sph"];
	}
}
