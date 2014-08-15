<?php

namespace pemapmodder\worldeditart\utils\subcommand;

use pemapmodder\worldeditart\utils\spaces\CuboidSpace;
use pocketmine\level\Position;
use pocketmine\math\Vector3;
use pocketmine\Player;
use pocketmine\utils\TextFormat;

class Cuboid extends Subcommand{
	public function getName(){
		return "cuboid";
	}
	public function getDescription(){
		return "Make a cuboid selection using your crosshair";
	}
	public function getUsage(){
		return "<s|shoot> <diagonal length> [a|adverse]  ".TextFormat::RED."OR".TextFormat::GREEN."  <g|grow> <x+> <y+> <z+> [x- = x+] [y- = y+] [z- = z+]";
	}
	public function getAliases(){
		return ["cub"];
	}
	public function checkPermission(/** @noinspection PhpUnusedParameterInspection */
		Player $player){
		return true; // TODO
	}
	public function onRun(array $args, Player $player){
		if(!isset($args[0])){
			return self::WRONG_USE;
		}
		$sub = array_shift($args);
		switch(strtolower($sub)){
			case "s":
			case "shoot":
				return $this->onShootRun($args, $player);
			case "g":
			case "grow":
				return $this->onGrowRun($args, $player);
			default:
				return self::WRONG_USE;
		}
	}
	public function onShootRun(array $args, Player $player){
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
		return "Cuboid selection set: $sel (".count($sel->getPosList())." blocks)";
	}
	public function onGrowRun(array $args, Player $player){
		if(!isset($args[2])){
			return self::WRONG_USE;
		}
		$reverse = false;
		if($args[0] === "r" or $args[0] === "reverse"){
			$reverse = true;
			array_shift($args);
		}
		foreach($args as $arg){
			if(!is_numeric($arg)){
				return self::WRONG_USE;
			}
		}
		$xp = (int) array_shift($args);
		$yp = (int) array_shift($args);
		$zp = (int) array_shift($args);
		$xm = $xp;
		$ym = $yp;
		$zm = $zp;
		if(isset($args[0])){
			$xm = (int) array_shift($args);
		}
		if(isset($args[0])){
			$ym = (int) array_shift($args);
		}
		if(isset($args[0])){
			$zm = (int) array_shift($args);
		}
		$from = new Vector3($xm, $ym, $zm);
		$to = new Vector3($xp, $yp, $zp);
		$level = $player->getLevel();
		if($reverse){
			$space = new CuboidSpace(Position::fromObject($to, $level), $from);
		}
		else{
			$space = new CuboidSpace(Position::fromObject($from, $level), $to);
		}
		$this->getMain()->setSelection($player, $space);
		return "Cuboid selection set: $space";
	}
}
