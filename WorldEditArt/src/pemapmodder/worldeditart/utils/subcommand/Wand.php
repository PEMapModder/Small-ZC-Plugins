<?php

namespace pemapmodder\worldeditart\utils\subcommand;

use pocketmine\item\Item;
use pocketmine\Player;

class Wand extends Subcommand{
	public function getName(){
		return "wand";
	}
	public function getDescription(){
		return "Check/select your wand item";
	}
	public function getUsage(){
		return "[hand|item id]";
	}
	public function onRun(array $args, Player $player){
		if(!isset($args[0])){
			if(!$player->hasPermission("wea.wand.check")){
				return self::NO_PERM;
			}
			$damageSpecified = 0;
			$item = $this->getMain()->getPlayerWand($player, $damageSpecified);
			$name = strtolower($item->getName()." with item damage ".($damageSpecified ? "specified as {$item->getDamage()}.":"not specified."));
			return "Your wand item is $name";
		}
		if($args[0] === "hand"){
			if(!$player->hasPermission("wea.wand.set.hand")){
				return self::NO_PERM;
			}
			$item = $player->getInventory()->getItemInHand();
			$damage = true;
			if(isset($args[1]) and $args[1] === "-d"){
				$damage = $item->getDamage();
			}
			$this->getMain()->setWand($player, $item->getID(), $damage);
			$name = strtolower($item->getName()." with item damage ".($damage !== true ? "specified as {$item->getDamage()}.":"not specified."));
			return "Your wand item is now $name";
		}
		if(!$player->hasPermission("wea.wand.set.named")){
			return self::NO_PERM;
		}
		$name = strtoupper(implode("_", $args));
		if(is_numeric(str_replace(":", "", $name))){
			$tokens = explode(":", $name);
			$damage = true;
			if(isset($tokens[1])){
				$damage = (int) $tokens[1];
			}
			$id = (int) $tokens[0];
			$this->getMain()->setWand($player, $id, $damage);
			$item = Item::get($id, $damage === true ? 0:$damage);
			$name = strtolower($item->getName()." with item damage ".($damage !== true ? "specified as {$item->getDamage()}.":"not specified."));
			return "Your wand item is now $name";
		}
		$damage = 0;
		$tokens = explode(":", $name);
		if(isset($tokens[1])){
			$name = $tokens[0];
			$damage = (int) $name;
		}
		$class = "pocketmine\\item\\".str_replace("_", "", $name);
		if(class_exists($class) and is_subclass_of($class, "pocketmine\\item\\Item")){
			/** @var Item $instance */
			$instance = new $class($damage);
			if(!isset($tokens[1])){
				$damage = true;
			}
			$this->getMain()->setWand($player, $instance->getID(), $damage);
		}
		elseif(defined("pocketmine\\item\\Item::$name")){
			$id = constant("pocketmine\\item\\Item::$name");
			if(!isset($tokens[1])){
				$damage = true;
			}
			$this->getMain()->setWand($player, $id, $damage);
			$instance = Item::get($id, $damage);
		}
		else{
			return self::WRONG_USE;
		}
		$name = strtolower($instance->getName()." with item damage ".($damage !== true ? "specified as {$instance->getDamage()}.":"not specified."));
		return "Your wand item is now $name";
	}
	public function checkPermission(Player $player){
		return $player->hasPermission("wea.wand.check") or $player->hasPermission("wea.wand.set.hand") or $player->hasPermission("wea.wand.set.named");
	}
}
