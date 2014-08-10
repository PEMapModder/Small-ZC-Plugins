<?php

namespace pemapmodder\worldeditart\utils\subcommand;

use pemapmodder\worldeditart\utils\provider\player\PlayerData;
use pocketmine\item\Item;
use pocketmine\Player;

class Jump extends Subcommand{
	public function getName(){
		return "jump";
	}
	public function getDescription(){
		return "Set/view own's jump";
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
			$arg = $args[0];
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
				/** @var PlayerData $data */
				$data = $this->getMain()->getPlayerData($player);
				$data->setJumpID($item->getID());
				$data->setJumpDamage($cd ? $item->getDamage():PlayerData::ALLOW_ANY);
				return "Your jump has been set.";
			case 1:
				$data = $this->getMain()->getPlayerData($player);
				$id = $data->getJumpID();
				$damage = $data->getJumpDamage();
				if($id === PlayerData::USE_DEFAULT){
					$id = $this->getMain()->getConfig()->get("jump-id");
				}
				if($damage === PlayerData::USE_DEFAULT){
					$damage = $this->getMain()->getConfig()->get("jump-damage");
				}
				if(is_int($damage)){
					return "Your jump is item $id:$damage. (Name: ".Item::get($id)->getName().")";
				}
				return "Your jump is item $id. (Name: ".Item::get($id)->getName().")";
		}
		return null;
	}
}
