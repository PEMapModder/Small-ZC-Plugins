<?php

namespace pemapmodder\worldeditart\utils\subcommand;

use pemapmodder\worldeditart\Main;
use pemapmodder\worldeditart\utils\spaces\CuboidSpace;
use pemapmodder\worldeditart\utils\spaces\CylinderSpace;
use pemapmodder\worldeditart\utils\spaces\SelectionExceedWorldException;
use pemapmodder\worldeditart\utils\spaces\Space;
use pemapmodder\worldeditart\utils\spaces\SphereSpace;
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
				if(!$player->hasPermission("wea.sel.pt.here")){
					return self::NO_PERM;
				}
				$pos = new Position($player->getX(), $player->getY(), $player->getZ(), $player->getLevel());
				$this->getMain()->setSelectedPoint($player, $pos);
				return "You have selected ".$this->posToStr($pos).".";
			case "1":
				if(!$player->hasPermission("wea.sel.cub.here")){
					return self::NO_PERM;
				}
				$this->setSelection($player, $player->getPosition(), null);
				return "";
			case "2":
				if(!$player->hasPermission("wea.sel.cub.here")){
					return self::NO_PERM;
				}
				$this->setSelection($player, null, $player->getPosition());
				return "";
			case "s1":
				if(!$player->hasPermission("wea.sel.cub.selpts")){
					return self::NO_PERM;
				}
				$sel = $this->getMain()->getSelectedPoint($player);
				if(!($sel instanceof Position)){
					return self::NO_SELECTED_POINT;
				}
				$this->setSelection($player, $sel, null);
				return "";
			case "s2":
				if(!$player->hasPermission("wea.sel.cub.selpts")){
					return self::NO_PERM;
				}
				$sel = $this->getMain()->getSelectedPoint($player);
				if(!($sel instanceof Position)){
					return self::NO_SELECTED_POINT;
				}
				$this->setSelection($player, null, $sel);
				return "";
			case "cub":
				if(!$player->hasPermission("wea.sel.cub.dir")){
					return self::NO_PERM;
				}
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
				$this->setSelection($player, $current, new Position($end->getX(), $end->getY(), $end->getZ(), $current->getLevel()));
				return "";
			case "cyl":
				if(!$player->hasPermission("wea.sel.cyl")){
					return self::NO_PERM;
				}
				if(!isset($args[1]) or !is_numeric($args[0]) or !is_numeric($args[0])){
					return "Usage: /wea sel cyl <height> <radius> [-here]";
				}
				$height = (int) array_shift($args);
				$radius = (int) array_shift($args);
				$sel = $this->getMain()->getSelectedPoint($player);
				if(isset($args[0]) and $args[0] === "-here"){
					$sel = $player->getPosition();
				}
				$vector = CylinderSpace::getVector($player->yaw, $player->pitch);
				$sel = new CylinderSpace($vector[0], $radius, $sel, $vector[1] ? -$height:$height);
				$this->setSelectionWithSpace($player, $sel);
				return "";
			case "sph":
				if(!$player->hasPermission("wea.sel.sph")){
					return self::NO_PERM;
				}
				if(!isset($args[0]) or !is_numeric($args[0])){
					return "Usage: /wea sel sph <radius> [-here]";
				}
				$radius = (int) $args[0];
				$sel = $this->getMain()->getSelectedPoint($player);
				if(isset($args[1]) and $args[1] === "-here"){
					$sel = $player->getPosition();
				}
				$sel = new SphereSpace($sel, $radius);
				$this->getMain()->setSelection($player, $sel);
				return "";
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
			try{
				$space = new CuboidSpace($pos1 === null ? $pos2:$pos1, $pos2 === null ? $pos2:$pos1);
			}
			catch(SelectionExceedWorldException $e){
				$player->sendMessage("Your selection has exceeded the world height limit (too high/too low). This command execution has been terminated.");
			}
		}
		else{
			if($space->getLevel()->getName() !== $pos1->getLevel()->getName() or $space->getLevel()->getName() !== $pos2->getLevel()->getName()){
				try{
					$space = new CuboidSpace($pos1, $pos2);
				}
				catch(SelectionExceedWorldException $e){
					$player->sendMessage("Your selection has exceeded the world height limit (too high/too low). This command execution has been terminated.");
					return;
				}
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
