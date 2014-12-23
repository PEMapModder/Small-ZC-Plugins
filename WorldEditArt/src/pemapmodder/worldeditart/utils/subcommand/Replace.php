<?php

namespace pemapmodder\worldeditart\utils\subcommand;

use pemapmodder\worldeditart\utils\spaces\BlockList;
use pemapmodder\worldeditart\utils\spaces\CuboidSpace;
use pemapmodder\worldeditart\utils\spaces\CylinderSpace;
use pemapmodder\worldeditart\utils\spaces\SingleList;
use pemapmodder\worldeditart\utils\spaces\Space;
use pemapmodder\worldeditart\utils\spaces\SphereSpace;
use pemapmodder\worldeditart\WorldEditArt;
use pocketmine\Player;

class Replace extends Subcommand{
	/** @var bool */
	private $twoNo, $twoYes;
	/** @var bool */
	private $mulNo, $mulYes;
	public function __construct(WorldEditArt $main, $twoNo, $twoYes, $mulNo, $mulYes){
		parent::__construct($main);
		$this->twoNo = $twoNo;
		$this->twoYes = $twoYes;
		$this->mulNo = $mulNo;
		$this->mulYes = $mulYes;
	}
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
		if($space instanceof CuboidSpace){
			return $player->hasPermission("wea.set.cuboid");
		}
		if($space instanceof CylinderSpace){
			return $player->hasPermission("wea.set.cylinder");
		}
		if($space instanceof SphereSpace){
			return $player->hasPermission("wea.set.sphere");
		}
		return $player->hasPermission("wea.set.*");
	}
	public function onRun(array $args, Space $space){
		if(!isset($args[1])){
			return self::WRONG_USE;
		}
		$from = BlockList::getBlockArrayFromString(array_shift($args));
		$targets = array_shift($args);
		$perc = strpos($targets, "%") !== false;
		$pos = strpos($targets, ",");
		if($pos === false){
			if(strpos(substr($targets, $pos), ",") === false){
				if(!$this->twoNo and !$perc){
					return "Replacing blocks into two block types without percentage is disabled on this server.";
				}
				if(!$this->twoYes and $perc){
					return "Replacing blocks into two block types with percentage is disabled on this server.";
				}
			}
			else{
				if(!$this->mulNo and !$perc){
					return "Replacing blocks into multiple block types without percentage is disabled on this server.";
				}
				if(!$this->mulYes and $perc){
					return "Replacing blocks into multiple block types with percentage is disabled on this server.";
				}
			}
			$to = new BlockList($targets);
		}
		else{
			$to = new SingleList(BlockList::getBlockFronString($targets));
		}
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
	public function getAliases(){
		return ["rpl"];
	}
}
