<?php

namespace pemapmodder\cmdsel\selector;

use pocketmine\command\CommandSender;
use pocketmine\Server;

class UsernameSelector implements Selector{
	public function getName(){
		return "username";
	}

	public function getAliases(){
		return ["u", "player"];
	}

	public function format(Server $server, CommandSender $sender, $name, array $args){
		return $sender->getName();
	}
}
