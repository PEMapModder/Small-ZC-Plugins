<?php

namespace pemapmodder\worldeditart\utils\subcommand;

use pemapmodder\worldeditart\events\TestSelectionEvent;
use pemapmodder\worldeditart\Main;
use pemapmodder\worldeditart\utils\spaces\CuboidSpace;
use pemapmodder\worldeditart\utils\spaces\CylinderSpace;
use pemapmodder\worldeditart\utils\spaces\Space;
use pemapmodder\worldeditart\utils\spaces\SphereSpace;
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
		return "<seconds> <block>";
	}
	public function checkPermission(Space $space, Player $player){
		$cuboid = "wea.test.cuboid";
		$sphere = "wea.test.sphere";
		$cylinder = "wea.test.cylinder";
		if($space instanceof CuboidSpace){
			return $player->hasPermission($cuboid);
		}
		if($space instanceof SphereSpace){
			return $player->hasPermission($sphere);
		}
		if($space instanceof CylinderSpace){
			return $player->hasPermission($cylinder);
		}
		// return $player->hasPermission($cuboid) or $player->hasPermission($sphere) or $player->hasPermission($cylinder)
		foreach($this->getMain()->getServer()->getPluginManager()->getPermission("wea.test")->getChildren() as $child){
			if($player->hasPermission($child)){
				return true;
			}
		}
		return false;
	}
	public function onRun(array $args, Space $sel, Player $player){
		if(!isset($args[1])){
			return self::WRONG_USE;
		}
		$length = array_shift($args);
		if(!is_numeric($length)){
			return self::WRONG_USE;
		}
		$length = (int) $length;
		$block = strtoupper(implode("_", $args));
		$block = Main::parseBlock($block);
		if(!($block instanceof Block)){
			return self::NO_BLOCK;
		}
		$this->getMain()->getServer()->getPluginManager()->callEvent($ev = new TestSelectionEvent($sel, $block, $length, $player));
		if($ev->isCancelled()){
			return $ev->getCancelMessage();
		}
		$cnt = $sel->setBlocks($block, $player);
		return "$cnt blocks have been changed.";
	}
}
