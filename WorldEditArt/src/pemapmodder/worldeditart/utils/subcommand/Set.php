<?php

namespace pemapmodder\worldeditart\utils\subcommand;

use pemapmodder\worldeditart\events\SetBlocksEvent;
use pemapmodder\worldeditart\Main;
use pemapmodder\worldeditart\utils\spaces\CuboidSpace;
use pemapmodder\worldeditart\utils\spaces\CylinderSpace;
use pemapmodder\worldeditart\utils\spaces\Space;
use pemapmodder\worldeditart\utils\spaces\SphereSpace;
use pocketmine\block\Block;
use pocketmine\Player;

class Set extends Subcommand{
	public function getName(){
		return "set";
	}
	public function getDescription(){
		return "Set a selection of blocks to the specified type";
	}
	public function getUsage(){
		return "<block>";
	}
	public function checkPermission(Space $space, Player $player){
		$cuboid = $player->hasPermission("wea.set.cuboid");
		$sphere = $player->hasPermission("wea.set.sphere");
		$cylinder = $player->hasPermission("wea.set.cylinder");
		if($space instanceof CuboidSpace){
			return $cuboid;
		}
		if($space instanceof SphereSpace){
			return $sphere;
		}
		if($space instanceof CylinderSpace){
			return $cylinder;
		}
		// return $cylinder or $cuboid or $sphere;
		$perm = $this->main->getServer()->getPluginManager()->getPermission("wea.set");
		foreach($perm->getChildren() as $child){
			if($player->hasPermission($child)){
				return true;
			}
		}
		return false;
	}
	public function onRun(array $args, Space $space, Player $player){
		if(!isset($args[0])){
			return self::WRONG_USE;
		}
		$block = strtoupper(implode("_", $args));
		$block = Main::parseBlock($block);
		if(!($block instanceof Block)){
			return self::NO_BLOCK;
		}
		$this->getMain()->getServer()->getPluginManager()->callEvent($ev = new SetBlocksEvent($space, $block, $player));
		if($ev->isCancelled()){
			return $ev->getCancelMessage();
		}
		$cnt = $space->setBlocks($block);
		return "$cnt blocks have been changed.";
	}
}
