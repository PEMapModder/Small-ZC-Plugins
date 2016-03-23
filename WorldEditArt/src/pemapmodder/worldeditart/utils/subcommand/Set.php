<?php

namespace pemapmodder\worldeditart\utils\subcommand;

use pemapmodder\worldeditart\utils\spaces\BlockList;
use pemapmodder\worldeditart\utils\spaces\BlockPatternParseException;
use pemapmodder\worldeditart\utils\spaces\CuboidSpace;
use pemapmodder\worldeditart\utils\spaces\CylinderSpace;
use pemapmodder\worldeditart\utils\spaces\SingleList;
use pemapmodder\worldeditart\utils\spaces\Space;
use pemapmodder\worldeditart\utils\spaces\SphereSpace;
use pemapmodder\worldeditart\WorldEditArt;
use pocketmine\Player;

class Set extends Subcommand{
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
		return "set";
	}

	public function getDescription(){
		return "Set all the blocks in your selection";
	}

	public function getUsage(){
		return "<blocks> [h|hollow] [nu|no-update]";
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
		if(!isset($args[0])){
			return self::WRONG_USE;
		}
		$name = array_shift($args);
		$perc = strpos($name, "%") !== false;
		if(($pos = strpos($name, ",")) !== false){
			if(strpos(substr($name, $pos), ",") === false){
				if(!$this->twoNo and !$perc){
					return "Setting two block types without percentage is disabled on this server.";
				}
				if(!$this->twoYes and $perc){
					return "Setting two block types with percentage is disabled on this server.";
				}
				if(!$this->mulNo and !$perc){
					return "Setting multiple block types without percentage is disabled on this server.";
				}
				if(!$this->mulYes and $perc){
					return "Setting multiple block types with percentage is disabled on this server.";
				}
			}
			try{
				$list = new BlockList($name);
			}catch(BlockPatternParseException $e){
				return "The following pattern error occurred: " . $e->getMessage();
			}
		}else{
			$block = BlockList::getBlockFronString($name);
			if($block === null){
				return self::NO_BLOCK;
			}
			$list = new SingleList($block);
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
		if($hollow){
			$cnt = $space->randomHollow($list, $update);
		}else{
			$cnt = $space->randomPlaces($list, $update);
		}
		return "$cnt block(s) have been changed.";
	}
}
