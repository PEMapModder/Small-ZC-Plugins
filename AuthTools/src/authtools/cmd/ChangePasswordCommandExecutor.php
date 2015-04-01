<?php

namespace authtools\cmd;

use authtools\AuthTools;
use pocketmine\command\Command;
use pocketmine\command\CommandExecutor;
use pocketmine\command\CommandSender;
use pocketmine\Player;

class ChangePasswordCommandExecutor implements CommandExecutor{
	/** @var AuthTools */
	private $main;
	public function __construct(AuthTools $main){
		$this->main = $main;
	}
	public function onCommand(CommandSender $sender, Command $cmd, $label, array $args){
		$sa = $this->main->sa;
		if(!($sender instanceof Player)){
			$sender->sendMessage($this->main->_->CmdInGameOnly);
			return true;
		}
		if($sa->isPlayerAuthenticated($sender)){
			if(!isset($args[1])){
				return false;
			}
			list($old, $new) = $args;
			$old = AuthTools::hash(strtolower($sender->getName()), $old);
			$new = AuthTools::hash(strtolower($sender->getName()), $new);
			unset($args); // why am I so paranoid...
			if(hash_equals($this->main->sa->getDataProvider()->getPlayer($sender)["hash"], $old)){

			}else{

			}
			return true;
		}else{
			$sender->sendMessage($cmd->getPermissionMessage());
			return true;
		}
	}
}
