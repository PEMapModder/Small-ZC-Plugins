<?php

namespace pemapmodder\lagfixer;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\network\protocol\AddPlayerPacket;
use pocketmine\network\protocol\PlayerArmorEquipmentPacket;
use pocketmine\network\protocol\PlayerEquipmentPacket;
use pocketmine\network\protocol\SetHealthPacket;
use pocketmine\Player;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\TextFormat;

class LagFixer extends PluginBase{
	public function onCommand(CommandSender $issuer, Command $command, $alias, array $args){
		if(!($issuer instanceof Player)){
			$issuer->sendMessage("I don't think you are supposed to see any players or your own health here.");
			return true;
		}
		switch($command->getName()){
			case "show":
				if(!isset($args[0])){
					return false;
				}
				$name = array_shift($args);
				if(strtolower($name) === "-all"){
					if(!($issuer->hasPermission("lagfixer.show.all"))){
						$issuer->sendMessage($command->getPermissionMessage());
						break;
					}
					$cnt = 0;
					foreach($this->getServer()->getOnlinePlayers() as $player){
						if($player->isOnline() and $issuer->getLevel()->getName() === $player->getLevel()->getName()){
							$packet = new AddPlayerPacket;
							$packet->clientID = 0;
							$packet->eid = $player->getID();
							$packet->metadata = $player->getData();
							$packet->pitch = $player->pitch;
							$packet->unknown1 = 0;
							$packet->unknown2 = 0;
							if($issuer->getRemoveFormat()){
								$packet->username = TextFormat::clean($player->getNameTag());
							}
							else{
								$packet->username = $player->getNameTag();
							}
							$packet->x = $player->x;
							$packet->y = $player->y;
							$packet->z = $player->z;
							$issuer->dataPacket($packet);
							$packet = new PlayerArmorEquipmentPacket;
							$packet->eid = $player->getID();
							$slots = [];
							for($i = 0; $i < 4; $i++){
								$slots[$i] = $player->getInventory()->getArmorItem($i)->getID();
							}
							$packet->slots = $slots;
							$issuer->dataPacket($packet);
							$packet = new PlayerEquipmentPacket;
							$packet->eid = $player->getID();
							$packet->item = $player->getInventory()->getItemInHand()->getID();
							$packet->meta = $player->getInventory()->getItemInHand()->getDamage();
							$packet->slot = 0;
							$issuer->dataPacket($packet);
						}
						$cnt++;
					}
					$issuer->sendMessage("$cnt players have been resent to you.");
					break;
				}
				else{
					if(!$issuer->hasPermission("lagfixer.show.player")){
						$issuer->sendMessage($command->getPermissionMessage());
						break;
					}
					$player = $this->getServer()->getPlayer($name);
					if(!($player instanceof Player)){
						$issuer->sendMessage("Player \"$name\" not found!");
						break;
					}
					if(!$player->isOnline() or $player->getLevel()->getName() !== $issuer->getLevel()->getName()){
						return $player->getName()." is not supposed to be seen by you!";
					}
					$packet = new AddPlayerPacket;
					$packet->clientID = 0;
					$packet->eid = $player->getID();
					$packet->metadata = $player->getData();
					$packet->pitch = $player->pitch;
					$packet->unknown1 = 0;
					$packet->unknown2 = 0;
					if($issuer->getRemoveFormat()){
						$packet->username = TextFormat::clean($player->getNameTag());
					}
					else{
						$packet->username = $player->getNameTag();
					}
					$packet->x = $player->x;
					$packet->y = $player->y;
					$packet->z = $player->z;
					$issuer->dataPacket($packet);
					$packet = new PlayerArmorEquipmentPacket;
					$packet->eid = $player->getID();
					$slots = [];
					for($i = 0; $i < 4; $i++){
						$slots[$i] = $player->getInventory()->getArmorItem($i)->getID();
					}
					$packet->slots = $slots;
					$issuer->dataPacket($packet);
					$packet = new PlayerEquipmentPacket;
					$packet->eid = $player->getID();
					$packet->item = $player->getInventory()->getItemInHand()->getID();
					$packet->meta = $player->getInventory()->getItemInHand()->getDamage();
					$packet->slot = 0;
					$issuer->dataPacket($packet);
					$issuer->sendMessage($player->getName()." has been resent to you.");
					break;
				}
			case "realhealth":
				$packet = new SetHealthPacket;
				$packet->health = $issuer->getHealth();
				$issuer->getMaxHealth();
				break;
		}
		return true;
	}
}
