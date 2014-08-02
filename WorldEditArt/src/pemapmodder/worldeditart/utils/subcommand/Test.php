<?php

namespace pemapmodder\worldeditart\utils\subcommand;

use pemapmodder\worldeditart\tasks\UndoTestTask;
use pemapmodder\worldeditart\utils\spaces\BlockList;
use pemapmodder\worldeditart\utils\spaces\Space;
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
		$name = array_shift($args);
		$block = BlockList::getBlockFronString($name);
		if($block === null){
			return self::NO_BLOCK;
		}
		$duration = 300;
		while(isset($args[0])){
			$arg = array_shift($args);
			if(is_numeric($arg)){
				$duration = intval($arg) * 20;
			}
			else{
				$replaces = BlockList::getBlockArrayFromString($arg);
			}
		}
		if(isset($replaces)){
			$space->replaceBlocks($replaces, $block, true, $player);
		}
		else{
			$space->setBlocks($block, $player);
		}
		$this->getMain()->getServer()->getScheduler()->scheduleDelayedTask(new UndoTestTask($this->getMain(), $space), $duration);
		return "Previewing the selection for ".($duration / 20)." seconds.";
	}
}
