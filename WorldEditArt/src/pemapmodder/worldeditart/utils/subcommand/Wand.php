<?php

namespace pemapmodder\worldeditart\utils\subcommand;

use pemapmodder\worldeditart\utils\provider\player\PlayerData;
use pocketmine\item\Item;
use pocketmine\Player;

class Wand extends Subcommand{
	public function getName(){
		return "wand";
	}
	public function getDescription(){
		return "Set/view own's wand";
	}
	public function getUsage(){
		return "[cd|check-damage|v|view]";
	}
	public function checkPermission(Player $player){
		return true; // TODO
	}
	public function onRun(array $args, Player $player){
		$cd = false;
		$mode = 0; // 0: set to held item; 1: set to
		while(isset($args[0])){
			$arg = array_shift($args);
			switch($arg){
				case "cd":
				case "check-damage":
					$cd = true;
					break;
				case "v":
				case "view":
					$mode = 1;
					break;
			}
		}
		switch($mode){
			case 0:
				$item = $player->getInventory()->getItemInHand();
				$this->getMain()->setWand($player, $item->getID(), $cd ? $item->getDamage():PlayerData::ALLOW_ANY);
				return "Your wand has been set.";
			case 1:
				$data = $this->getMain()->getPlayerData($player);
				$id = $data->getWandID();
				$damage = $data->getWandDamage();
				if($id === PlayerData::USE_DEFAULT){
					$id = $this->getMain()->getConfig()->get("wand-id");
				}
				if($damage === PlayerData::USE_DEFAULT){
					$damage = $this->getMain()->getConfig()->get("wand-damage");
				}
				if(is_int($damage)){
					return "Your wand is item $id:$damage. (Name: ".Item::get($id)->getName().")";
				}
				return "Your wand is item $id. (Name: ".Item::get($id)->getName().")";
		}
		return null;
	}
}
