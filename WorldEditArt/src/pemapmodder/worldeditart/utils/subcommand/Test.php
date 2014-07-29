<?php

namespace pemapmodder\worldeditart\utils\subcommand;

use pemapmodder\worldeditart\tasks\UndoTestTask;
use pemapmodder\worldeditart\utils\spaces\BlockList;
use pemapmodder\worldeditart\utils\spaces\Space;
use pocketmine\block\Block;
use pocketmine\Player;

class Test extends Subcommand{
	public function getName(){
		return "test";
	}
	public function getDescription(){
		return "Test your selection";
	}
	public function getUsage(){
		return "/w test <to> [from] [duration = 15]";
	}
	public function checkPermission(Space $space, Player $player){
		// TODO
	}
	public function onRun(array $args, Space $space, Player $player){
		if(!isset($args[0])){
			return self::WRONG_USE;
		}
		$tokens = explode(":", array_shift($args));
		$block = BlockList::parseBlock($tokens[0]);
		if($block === null){
			return self::NO_BLOCK;
		}
		$block = Block::get($block, (isset($tokens[1]) and is_numeric($tokens[1])) ? intval($tokens[1]):0);
		$duration = 300;
		while(isset($args[0])){
			if(is_numeric($args[0])){
				$duration = intval(array_shift($args)) * 20;
			}
			else{
				$tokens = explode(":", array_shift($args));
				$replace = BlockList::parseBlock($tokens[0]);
				if($replace === null){
					return self::NO_BLOCK;
				}
				$replace = Block::get($replace, (isset($tokens[1]) and is_numeric($tokens[1])) ? intval($tokens[1]):0);
			}
		}
		if(isset($replace)){
			$space->replaceBlocks($replace, $block, true, $player);
		}
		else{
			$space->setBlocks($block, $player);
		}
		$this->getMain()->getServer()->getScheduler()->scheduleDelayedTask(new UndoTestTask($this->getMain(), $space), $duration);
		return "Previewing the selection for ".($duration / 20)." seconds.";
	}
}
