<?php

namespace pemapmodder\worldeditart\utils\subcommand;

use pemapmodder\worldeditart\utils\spaces\CuboidSpace;
use pemapmodder\worldeditart\utils\spaces\CylinderSpace;
use pemapmodder\worldeditart\utils\spaces\Space;
use pemapmodder\worldeditart\utils\spaces\SphereSpace;
use pocketmine\block\Block;
use pocketmine\level\Position;
use pocketmine\permission\Permission;
use pocketmine\Player;

class Copy extends Subcommand{
	public function getName(){
		return "copy";
	}
	public function getDescription(){
		return "Copy your current selection into your clipboard";
	}
	public function getUsage(){
		return "[-a] [-g <name>]";
	}
	public function checkPermission(Space $selection, Player $player){
		$root = $this->getPermissionRoot();
		$cuboid = $root."cuboid";
		$cylinder = $root."cylinder";
		$sphere = $root."sphere";
		if($selection instanceof CuboidSpace){
			return $player->hasPermission($cuboid);
		}
		if($selection instanceof CylinderSpace){
			return $player->hasPermission($cylinder);
		}
		if($selection instanceof SphereSpace){
			return $player->hasPermission($sphere);
		}
		$perm = $this->getMain()->getServer()->getPluginManager()->getPermission(trim($root, "."));
		if($perm instanceof Permission){
			foreach($perm->getChildren() as $child){
				if($player->hasPermission($child)){
					return true;
				}
			}
		}
		return false;
	}
	protected function getPermissionRoot(){
		return "wea.clipboard.copy.";
	}
	public function onRun(array $args, Space $selection, Player $player){
		$anchor = in_array("-a", $args);
		if($anchor){
			$ref = $this->getMain()->getAnchor($player);
			if(!($ref instanceof Position)){
				return self::NO_ANCHOR;
			}
		}
		else{
			$ref = $player->getPosition();
		}
		$global = array_search("-g", $args);
		if(is_int($global)){
			if(!isset($args[$global + 1])){
				return "Please give a name for the global clip.";
			}
			$global = implode(" ", array_slice($args, $global + 1));
			if(trim($global) === ""){
				return "Invalid global clip name (please enter a valid file";
			}
		}
		$ref = Position::fromObject($ref->floor(), $ref->getLevel());
		$list = $selection->getPosList();
		$blocks = [];
		foreach($list as $pos){
			$block = $pos->getLevel()->getBlock($pos);
			$blocks[] = Block::get($block->getID(), $block->getDamage(), Position::fromObject($pos->subtract($ref), $pos->getLevel()));
		}
		$data = [
			"author" => $player->getName(),
			"blocks" => $blocks,
			"yaw" => $player->yaw
		];
		if(is_string($global)){
			if($this->getMain()->isGlobalClipCreated($global)){
				return "Clip \"$global\" already exists!";
			}
			$result = $this->getMain()->saveGlobalClip($global, $data);
			if($result !== true){
				return "Cannot write to global clipboard. Reason: $result";
			}
		}
		else{
			$this->getMain()->setClip($player, $data);
		}
		return $this->onPostRun($blocks, $selection);
	}
	protected function onPostRun(array $blocks){
		return "A selection of ".count($blocks)." have been copied.";
	}
}
