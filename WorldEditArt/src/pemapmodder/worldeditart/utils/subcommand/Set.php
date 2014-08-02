<?php

namespace pemapmodder\worldeditart\utils\subcommand;

use pemapmodder\worldeditart\utils\spaces\BlockList;
use pemapmodder\worldeditart\utils\spaces\Space;
use pocketmine\Player;

class Set extends Subcommand{
	public function getName(){
		return "set";
	}
	public function getDescription(){
		return "Set all the blocks in your selection";
	}
	public function getUsage(){
		return "/w set <block>";
	}
	public function checkPermission(Space $space, Player $player){
		// TODO
	}
	public function onRun(array $args, Space $space, Player $player){
		if(!isset($args[0])){
			return self::WRONG_USE;
		}
		$block = BlockList::getBlockFronString(array_shift($args));
		// TODO
	}
}
