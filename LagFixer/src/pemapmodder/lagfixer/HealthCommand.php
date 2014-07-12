<?php

namespace pemapmodder\lagfixer;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\command\PluginIdentifiableCommand;
use pocketmine\network\protocol\SetHealthPacket;
use pocketmine\Player;

class HealthCommand extends Command implements PluginIdentifiableCommand{
	private $plugin;
	public function __construct(LagFixer $main){
		$this->plugin = $main;
		parent::__construct("realhealth", "Sends you your real health", null, ["rh", "rlhlth"]);
		$this->setPermission("lagfixer.realhealth");
		$this->setPermissionMessage("Hmm, seems that you haven't been granted permission to view your real health.");
	}
	public function execute(CommandSender $sender, $alias, array $args){
		if($sender instanceof Player){
			$packet = new SetHealthPacket;
			$packet->health = $sender->getHealth();
			$sender->dataPacket($packet);
			return true;
		}
		$sender->sendMessage("Please run this command in-game. If you want to know your device's health, don't ask me.");
		return false;
	}
	public function getPlugin(){
		return $this->plugin;
	}
}
