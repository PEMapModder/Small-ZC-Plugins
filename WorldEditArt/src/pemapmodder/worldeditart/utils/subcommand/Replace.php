<?php

namespace pemapmodder\worldeditart\utils\subcommand;

use pemapmodder\worldeditart\utils\spaces\BlockList;
use pemapmodder\worldeditart\utils\spaces\Space;
use pocketmine\Player;

class Replace extends Subcommand{
	public function getName(){
		return "replace";
	}
	public function getDescription(){
		return "Replace specified type(s) of blocks into other type(s)";
	}
	public function getUsage(){
		return "<from> <to> [nu|no-update] [h|hollow]";
	}
	public function checkPermission(Space $space, Player $player){
		// TODO
	}
	public function onRun(array $args, Space $space){
		if(!isset($args[1])){
			return self::WRONG_USE;
		}
		$from = BlockList::getBlockArrayFromString(array_shift($args));
		$to = new BlockList(array_shift($args));
		$hollow = false;
		$update = true;
		while(isset($args[0])){
			switch($arg = strtolower(array_shift($args))){
				case "h":
				case "hollow":
					$hollow = true;
					break;
				case "nu":
				case "no-update":
					$update = false;
					break;
			}
		}
		if($hollow){
			$cnt = $space->randomHollowReplace($from, $to, $update);
		}
		else{
			$cnt = $space->randomReplaces($from, $to, $update);
		}
		return "$cnt block(s) have been changed.";
	}
}
