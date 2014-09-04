<?php

namespace pemapmodder\cmdsel\selector;

use pemapmodder\cmdsel\CmdSel;
use pocketmine\command\CommandSender;
use pocketmine\level\Position;
use pocketmine\Player;
use pocketmine\Server;

class NearestSelector implements Selector{
	public function getName(){
		return "p";
	}
	public function getAliases(){
		return ["near", "n"];
	}
	public function format(Server $server, CommandSender $sender, $name, array $args){
		if(!($sender instanceof Position)){
			return null;
		}
		/** @var CommandSender|Position $sender */
		$players = CmdSel::getNearestPlayers($sender, [
			function(Player $player) use($args, $sender){
				return CmdSel::checkSelectors($args, $sender, $player);
			}
		]);
		/** @var Player $rand */
		$rand = array_rand($players);
		return $rand->getName();
	}
}
