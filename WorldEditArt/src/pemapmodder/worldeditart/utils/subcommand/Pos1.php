<?php

namespace pemapmodder\worldeditart\utils\subcommand;

use pemapmodder\worldeditart\Main;
use pocketmine\level\Position;
use pocketmine\Player;

class Pos1 extends Subcommand{
	public function getName(){
		return "pos1";
	}
	public function getDescription(){

	}
	public function getUsage(){
		return "/w pos1 [c]";
	}
	public function checkPermission(Player $player){
		// TODO
	}
	public function onRun(array $args, Player $player){
		$selected = $player->getPosition();
		if(in_array("c", $args)){
			$selected = Main::getCrosshairTarget($player);
			if(!($selected instanceof Position)){
				return "The block is too far/in the void/in the sky.";
			}
		}
		$pos = $this->getMain()->getTempPos($player);
		if(!$pos["#"]){
			$this->getMain()->setTempPos($player, $selected, false);
		}
		// TODO
	}
}
