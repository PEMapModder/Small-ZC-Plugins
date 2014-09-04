<?php

namespace pemapmodder\cmdsel\selector;

use pemapmodder\cmdsel\CmdSel;
use pocketmine\command\CommandSender;
use pocketmine\Server;

class RandomSelector implements Selector{
	public function getName(){
		return "random";
	}
	public function getAliases(){
		return ["r"];
	}
	public function format(Server $server, CommandSender $sender, $name, array $args){
		$players = [];
		foreach($sender->getServer()->getOnlinePlayers() as $player){
			if($player === $sender){
				continue;
			}
			if(CmdSel::checkSelectors($args, $sender, $player)){
				continue;
			}
			$players[] = $player;
		}
		if(count($players) === 0){
			return false;
		}
		/** @var \pocketmine\Player $rand */
		$rand = array_rand($players);
		return $rand;
	}
}
