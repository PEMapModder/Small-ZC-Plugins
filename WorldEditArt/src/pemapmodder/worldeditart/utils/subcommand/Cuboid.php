<?php

namespace pemapmodder\worldeditart\utils\subcommand;

use pemapmodder\worldeditart\utils\spaces\CuboidSpace;
use pocketmine\level\Position;
use pocketmine\Player;

class Cuboid extends Subcommand{
	public function getName(){
		return "cuboid";
	}
	public function getDescription(){
		return "Make a cuboid selection using your crosshair";
	}
	public function getUsage(){
		return "<diagonal length> [a|adverse]";
	}
	public function getAliases(){
		return ["cub"];
	}
	public function checkPermission(Player $player){
		// TODO
	}
	public function onRun(array $args, Player $player){
		if(!isset($args[0]) or !is_numeric($args[0])){
			return self::WRONG_USE;
		}
		$length = floatval(array_shift($args));
		$p1 = $player->getPosition()->floor();
		$p2 = $player->add($player->getDirectionVector()->multiply($length))->floor();
		if($p1->y < 0 or $p1->y > (defined($path = "pemapmodder\\worldeditart\\MAX_WORLD_HEIGHT") ? constant($path):127)){
			return "You must be inside the building height!";
		}
		elseif($p2->y < 0 or $p2->y > (defined($path = "pemapmodder\\worldeditart\\MAX_WORLD_HEIGHT") ? constant($path):127)){
			return "The selected area exceeded the world height limit!";
		}
		$player->getLevel()->loadChunk($p2->x >> 4, $p2->z >> 4);
		while(isset($args[0])){
			$arg = array_shift($args);
			switch($arg){
				case "a":
				case "adverse":
					$cache = [$p1, $p2];
					$p1 = $cache[1];
					$p2 = $cache[0];
					break;
			}
		}
		$level = $player->getLevel();
		$this->getMain()->setSelection($player,
			$sel = new CuboidSpace($p1 = Position::fromObject($p1, $level), $p2 = Position::fromObject($p2, $level)));
		return "Cuboid selection set: ({$p1->x}, {$p2->y}, {$p2->z})-({$p2->x}, {$p2->y}, {$p2->z}) (".count($sel->getPosList())." blocks)";
	}
}
