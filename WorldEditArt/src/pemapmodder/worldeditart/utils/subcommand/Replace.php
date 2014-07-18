<?php

namespace pemapmodder\worldeditart\utils\subcommand;

use pemapmodder\worldeditart\events\ReplaceEvent;
use pemapmodder\worldeditart\Main;
use pemapmodder\worldeditart\utils\spaces\CuboidSpace;
use pemapmodder\worldeditart\utils\spaces\CylinderSpace;
use pemapmodder\worldeditart\utils\spaces\Space;
use pemapmodder\worldeditart\utils\spaces\SphereSpace;
use pocketmine\block\Block;
use pocketmine\Player;

class Replace extends Subcommand{
	public function getName(){
		return "replace";
	}
	public function getDescription(){
		return "Replace a type of block in a selection to another type";
	}
	public function getUsage(){
		return "<from> <to> [-r <percentage>";
	}
	public function checkPermission(Space $space, Player $player){
		$cuboid = "wea.replace.cuboid";
		$cylinder = "wea.replace.cylinder";
		$sphere = "wea.replace.sphere";
		if($space instanceof CuboidSpace){
			return $player->hasPermission($cuboid);
		}
		if($space instanceof CylinderSpace){
			return $player->hasPermission($cylinder);
		}
		if($space instanceof SphereSpace){
			return $player->hasPermission($sphere);
		}
		$perm = $this->getMain()->getServer()->getPluginManager()->getPermission("wea.replace");
		foreach($perm->getChildren() as $child){
			if($player->hasPermission($child)){
				return true;
			}
		}
		return false;
	}
	public function onRun(array $args, Space $space, Player $player){
		if(!isset($args[1])){
			return self::WRONG_USE;
		}
		$from = array_shift($args);
		$to = array_shift($args);
		$from = Main::parseBlock($from);
		$to = Main::parseBlock($to);
		if(!($from instanceof Block) or !($to instanceof Block)){
			return self::NO_BLOCK;
		}
		$percentage = false;
		if(isset($args[0]) and array_shift($args) === "-r"){
			if(!isset($args[0])){
				return self::WRONG_USE;
			}
			$percentage = floatval(array_shift($args));
		}
		$this->getMain()->getServer()->getPluginManager()->callEvent($ev = new ReplaceEvent($space, $from, $to, $player, $percentage));
		if($ev->isCancelled()){
			return $ev->getCancelMessage();
		}
		$percentage = $ev->getPercentage();
		$from = $ev->getFrom();
		$to = $ev->getTo();
		if($percentage === false){
			$cnt = $space->replaceBlocks($from, $to);
		}
		else{
			$cnt = $space->randomReplaces($from, $to, $percentage);
		}
		return "$cnt blocks have been changed.";
	}
}
