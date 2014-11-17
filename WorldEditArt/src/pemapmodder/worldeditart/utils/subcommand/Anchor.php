<?php

namespace pemapmodder\worldeditart\utils\subcommand;

use pemapmodder\worldeditart\WorldEditArt;
use pocketmine\block\Block;
use pocketmine\level\Position;
use pocketmine\Player;

class Anchor extends Subcommand{
	public function getName(){
		return "anchor";
	}
	public function getDescription(){
		return "Set/view your anchor";
	}
	public function getUsage(){
		return "[v|view|c|crosshair]";
	}
	public function checkPermission(/** @noinspection PhpUnusedParameterInspection */
		Player $player){
		return true; // TODO
	}
	public function onRun(array $args, Player $player){
		$mode = 0;
		$target = $player->getPosition();
		while(isset($args[0])){
			$arg = array_shift($args);
			switch($arg){
				case "c":
				case "crosshair":
					$target = WorldEditArt::getCrosshairTarget($player);
					if(!($target instanceof Block)){
						return "The block is too far/in the void/sky!";
					}
					break;
				case "v":
				case "view":
					$mode = 1;
					break;
			}
		}
		switch($mode){
			case 0:
				$this->getMain()->setAnchor($player, $target);
				return "Your anchor has been set to ".WorldEditArt::posToStr($target).".";
			case 1:
				$anchor = $this->getMain()->getAnchor($player);
				if(!($anchor instanceof Position)){
					return "You don't have an anchor selected!";
				}
				return "Your anchor is at ".WorldEditArt::posToStr($anchor).".";
			default:
				return null;
		}
	}
}
