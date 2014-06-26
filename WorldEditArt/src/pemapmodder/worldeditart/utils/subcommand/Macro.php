<?php

namespace pemapmodder\worldeditart\utils\subcommand;

use pocketmine\level\Position;
use pocketmine\Player;

class Macro extends Subcommand{
	public function getName(){
		return "macro";
	}
	public function getDescription(){
		return "Manage WorldEditArt macros";
	}
	public function getUsage(){
		return "TODO"; // TODO
	}
	public function checkPermission(Position $selectedPoint, Player $player){
		return false; // TODO
	}
	public function onRun(array $args, Position $selectedPoint, Player $player){
		// TODO
	}
}
