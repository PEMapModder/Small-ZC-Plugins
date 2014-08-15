<?php

namespace pemapmodder\worldeditart\utils\subcommand;

use pemapmodder\worldeditart\Main;
use pemapmodder\worldeditart\utils\spaces\CylinderSpace;
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
		return "<radius> <height> [d <m|me|u|up|d|down|l|left|r|right|b|back>] [a|anchor]";
	}
	public function checkPermission(/** @noinspection PhpUnusedParameterInspection */
		Player $player){
		return true; // TODO
	}
	public function onRun(array $args, Player $player){
		if(!isset($args[0])){
			return self::WRONG_USE;
		}
		$center = $player->floor();
		$level = $player->getLevel();
		$radius = floatval(array_shift($args));
		$height = (int) array_shift($args);
		$axis = Main::directionNumber2Array($player->getDirection());
		while(count($args) > 0){
			$arg = array_shift($args);
			switch($arg){
				case "a":
				case "anchor":
					$anchor = $this->getMain()->getAnchor($player);
					if(!($anchor instanceof Position)){
						return self::NO_ANCHOR;
					}
					$center = $anchor->floor();
					break;
				case "d":
					$d = array_shift($args);
					switch(strtolower($d)){
						case "m":
						case "me":
							break;
						case "u":
						case "up":
							$axis = [CylinderSpace::Y, CylinderSpace::PLUS];
							break;
						case "d":
						case "down":
							$axis = [CylinderSpace::Y, CylinderSpace::MINUS];
							break;
						case "l":
						case "left":
							$axis = Main::directionNumber2Array(Main::rotateDirectionNumberClockwise($player->getDirection(), 3));
							break;
						case "r":
						case "right":
							$axis = Main::directionNumber2Array(Main::rotateDirectionNumberClockwise($player->getDirection(), 1));
							break;
						case "b":
						case "back":
							$axis = Main::directionNumber2Array(Main::rotateDirectionNumberClockwise($player->getDirection(), 2));
							break;

					}
					break;
			}
		}
		$center = Position::fromObject($center, $level);
		$space = new CylinderSpace($axis[0], $radius, $center, $height * $axis[1]);
		$this->getMain()->setSelection($player, $space);
		return "Your selection is now $space.";
	}
	public function getAliases(){
		return ["cyl"];
	}
}
