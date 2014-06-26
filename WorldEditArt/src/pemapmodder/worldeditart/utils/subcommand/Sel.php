<?php

namespace pemapmodder\worldeditart\utils\subcommand;

use pemapmodder\worldeditart\Main;
use pemapmodder\worldeditart\utils\spaces\CuboidSpace;
use pemapmodder\worldeditart\utils\spaces\Space;
use pocketmine\level\Position;
use pocketmine\Player;

class Sel extends Subcommand{
	public function getName(){
		return "sel";
	}
	public function getDescription(){
		return "Make point/cuboid/cylinder/sphere selection";
	}
	public function getUsage(){
		return "<here|1|2|cub|cyl|sph|test>";
	}
	public function checkPermission(Player $player){
		return $player->hasPermission("wea.sel.pt.here") or $player->hasPermission("wea.sel.cub") or $player->hasPermission("wea.sel.cyl") or $player->hasPermission("wea.sel.sph");
	}
	public function onRun(array $args, Player $player){
		if(!isset($args[0])){
			return self::WRONG_USE;
		}
		switch($cmd = strtolower(array_shift($args))){ // not another subcommand map!!! :P
			case "here":
				$pos = new Position($player->getX(), $player->getY(), $player->getZ(), $player->getLevel());
				$this->getMain()->setSelectedPoint($player, $pos);
				return "You have selected ".$this->posToStr($pos).".";
			case "1":
				if(!$player->hasPermission("wea.sel.cubpts")){
					return self::NO_PERM;
				}
				$this->setSelection($player, $player->getPosition(), null);
				return "";
			case "2":
				if(!$player->hasPermission("wea.sel.cubpts")){
					return self::NO_PERM;
				}
				$this->setSelection($player, null, $player->getPosition());
				return "";
			case "cub":
				if(!isset($args[0])){
					$player->sendMessage("Usage: /wea sel cub <distance> [-s] Select a cuboid starting from your position (or your selected point if -s is given)");
					return true;
				}
				$distance = (int) array_shift($args);
				$current = $player->getPosition();
				if(isset($args[0]) and $args[0] === "-s"){
					$current = $this->getMain()->getSelectedPoint($player);
				}
				$end = $current->add($player->getDirectionVector()->multiply($distance));
				$this->setSelection($player, $current, $end);
				return "";
			case "cyl":
				break;
			case "sph":
				break;
		}
		return false;
	}
	private function setSelectionWithSpace(Player $player, Space $space, $silent = false){
		$this->getMain()->setSelection($player, $space);
		if(!$silent){
			$player->sendMessage("Your selection is now $space.");
		}
	}
	private function setSelection(Player $player, Position $pos1, Position $pos2, $silent = false){
		$space = $this->getMain()->getSelection($player);
		if(!($space instanceof CuboidSpace)){
			$space = new CuboidSpace($pos1 === null ? $pos2:$pos1, $pos2 === null ? $pos2:$pos1);
		}
		else{
			if($space->getLevel()->getName() !== $pos1->getLevel()->getName() or $space->getLevel()->getName() !== $pos2->getLevel()->getName()){
				$space = new CuboidSpace($pos1, $pos2);
			}
			else{
				$space->set0($pos1);
				$space->set1($pos2);
			}
		}
		$this->setSelectionWithSpace($player, $space, $silent);
	}
	public static function posToStr(Position $pos){
		return Main::posToStr($pos);
	}
}
