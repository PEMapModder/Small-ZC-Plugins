<?php

namespace pemapmodder\infoess;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\item\Item;
use pocketmine\Player;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\TextFormat;

class InfoEss extends PluginBase{
	public function onCommand(CommandSender $sender, Command $cmd, $alias, array $args){
		$order = $this->mapCmd($cmd->getName());
		if(isset($args[$order])){
			$player = $this->getServer()->getPlayer($args[$order]);
			if(!($player instanceof Player)){
				$sender->sendMessage("Player $args[$order] not found.");
				return true;
			}
			unset($args[$order]);
			$args = array_values($args);
		}
		else{
			$player = $sender;
			if(!($player instanceof Player)){
				return false;
			}
		}
		return $this->handle($cmd->getName(), $args, $player, $sender);
	}
	private function handle($cmd, $args, Player $player, CommandSender $issuer){
		switch($cmd){
			case "getping":
				$issuer->sendMessage("Ping of ".$player->getName().": unknown ms");
				return true;
			case "seearmor":
				$issuer->sendMessage("Armor of ".$player->getName().":");
				$issuer->sendMessage("Helmet: ".$this->formatItem($player->getInventory()->getArmorItem(0)));
				$issuer->sendMessage("Chestplate: ".$this->formatItem($player->getInventory()->getArmorItem(1)));
				$issuer->sendMessage("Leggings: ".$this->formatItem($player->getInventory()->getArmorItem(2)));
				$issuer->sendMessage("Boots: ".$this->formatItem($player->getInventory()->getArmorItem(3)));
				return true;
			case "seegm":
				$issuer->sendMessage("Gamemode of ".$player->getName().": ".$this->formatGamemode($player->getGamemode()));
				return true;
			case "getpos":
				$issuer->sendMessage($player->getName()." is at (".TextFormat::YELLOW.$player->x.", ".TextFormat::GREEN.$player->y.", ".TextFormat::AQUA.$player->z.") in world ".TextFormat::RED.$player->getLevel()->getName().".");
				return true;
			case "setarmor":
				// TODO
				break;
			case "rmarmor":
				// TODO
				break;
			case "sessions":
				// TODO
				break;
		}
		return false;
	}
	public static function formatGamemode($gm){
		switch($gm){
			case Player::SURVIVAL:
				return TextFormat::GREEN."Survival";
			case Player::CREATIVE:
				return TextFormat::RED."Creative";
			case Player::ADVENTURE:
				return TextFormat::BLUE."Adventure";
		}
		return TextFormat::YELLOW."Spectator";
	}
	public static function formatItem(Item $item){
		$item = (int) round($item->getID() / 4);
		switch($item){
			case (Item::CHAIN_LEGGINGS / 4):
				return TextFormat::GRAY."Chain";
			case (Item::DIAMOND_LEGGINGS / 4):
				return TextFormat::BLUE."Diamond";
			case (Item::GOLD_LEGGINGS / 4):
				return TextFormat::GOLD."Gold";
			case (Item::IRON_LEGGINGS / 4):
				return TextFormat::GREEN."Iron"; // Iron(II) has greenish color
			case (Item::LEATHER_PANTS / 4):
				return TextFormat::RED;
		}
		return TextFormat::WHITE."None";
	}
	private function mapCmd($name){
		switch($name){
			case "getping":
				return 0;
			case "seearmor":
				return 0;
			case "seegm":
				return 0;
			case "getpos":
				return 0;
			case "setarmor":
				return 2;
			case "rmarmor":
				return 1;
			case "sessions":
				return 0;
		}
		return null;
	}
}
