<?php

namespace pemapmodder\worldeditart\utils\subcommand;

use pocketmine\level\Position;
use pocketmine\Player;

class Cylinder extends Subcommand{
	public function getName(){
		return "cylinder";
	}
	public function getDescription(){
		return "Make a cylinder selection";
	}
	public function getUsage(){
		return "<radius> <height> [d <m|me|u|up|d|down|l|left|r|right|back|e|east|s|south|w|west|n|north>] [a|anchor]";
	}
	public function checkPermission(Player $player){
		// TODO
	}
	public function onRun(array $args, Player $player){
		if(!isset($args[0])){
			return self::WRONG_USE;
		}
		$center = $player->getPosition()->floor();
		$radius = array_shift($args);
		$height = array_shift($args);
		while(count($args) > 0){
			$arg = array_shift($args);
			switch($arg){
				case "a":
				case "anchor":
					$anchor = $this->getMain()->getAnchor($player);
					if(!($anchor instanceof Position)){
						return self::NO_ANCHOR;
					}
			}
		}
	}
}
