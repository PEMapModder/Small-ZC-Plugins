<?php

namespace pemapmodder\cmdsel\selector;

use pemapmodder\cmdsel\CmdSel;
use pocketmine\command\CommandSender;
use pocketmine\Server;

class AllRecursiveSelector implements RecursiveSelector{
	public function getName(){
		return "all";
	}
	public function getAliases(){
		return ["a"];
	}
	public function format(Server $server, CommandSender $sender, $name, array $args){
		$result = [];
		foreach($server->getOnlinePlayers() as $p){
			if(CmdSel::checkSelectors($args, $sender, $p)){
				$result[] = $p->getName();
			}
		}
		return $result;
	}
}
