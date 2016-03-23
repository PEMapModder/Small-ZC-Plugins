<?php

namespace pemapmodder\cmdsel\selector;

use pocketmine\command\CommandSender;
use pocketmine\level\Position;
use pocketmine\Server;

class WorldSelector implements Selector{
	public function getName(){
		return "world";
	}

	public function getAliases(){
		return ["w"];
	}

	public function format(Server $server, CommandSender $sender, $name, array $args){
		if($sender instanceof Position){ // command blocks?
			return $sender->getLevel()->getName();
		}
		return false;
	}
}
