<?php

namespace pemapmodder\worldeditart\utils\subcommand;

use pemapmodder\worldeditart\utils\spaces\BlockList;
use pemapmodder\worldeditart\utils\spaces\BlockPatternParseException;
use pemapmodder\worldeditart\utils\spaces\SingleList;
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
		return "<blocks> [h|hollow] [nu|no-update]";
	}
	public function checkPermission(Space $space, Player $player){
		// TODO
	}
	public function onRun(array $args, Space $space){
		if(!isset($args[0])){
			return self::WRONG_USE;
		}
		$name = array_shift($args);
		if(strpos($name, ",") !== false){
			try{
				$block = new BlockList(array_shift($args));
			}
			catch(BlockPatternParseException $e){
				return "The following pattern error occurred: ".$e->getMessage();
			}
		}
		else{
			$block = BlockList::getBlockFronString($name);
			if($block === null){
				return self::NO_BLOCK;
			}
		}
		$hollow = false;
		$update = false;
		while(isset($args[0])){
			$arg = array_shift($args);
			switch($arg){
				case "h":
				case "hollow":
					$hollow = true;
				case "nu":
				case "no-update":
					$update = true;
					break;
			}
		}
		if($block instanceof BlockList){
			if($hollow){
				$cnt = $space->randomHollow($block, $update);
			}
			else{
				$cnt = $space->randomPlaces($block, $update);
			}
		}
		else{
			if($hollow){
				$cnt = $space->randomHollow(new SingleList($block), $update);
			}
			else{
				$cnt = $space->setBlocks($block, false, $update);
			}
		}
		return "$cnt block(s) have been changed.";
	}
}
