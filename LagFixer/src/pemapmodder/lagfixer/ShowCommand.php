<?php

namespace pemapmodder\lagfixer;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\command\PluginIdentifiableCommand;
use pocketmine\Player;

class ShowCommand extends Command implements PluginIdentifiableCommand{
	private $plugin;

	public function __construct(LagFixer $main){
		$this->plugin = $main;
		parent::__construct("show", "Force show a player(s) if they are not supposed to be invisible", "<player|-all>");
		$this->setPermission("lagfixer.show");
		$this->setPermissionMessage("Seems that someone took away your permission to show invisible players.");
	}

	public function execute(CommandSender $issuer, $alias, array $args){
		if($issuer instanceof Player){
			if(isset($args[0])){
				if(($name = strtolower($args[0])) === "-all"){
					if(!$issuer->hasPermission("lagfixer.show.all")){
						$issuer->sendMessage("You don't have permission to show all invisible players in once.");
						return false;
					}
					foreach($this->plugin->getServer()->getOnlinePlayers() as $player){
						if($player->getID() !== $issuer->getID()){
							$player->spawnTo($issuer);
						}
					}
					$issuer->sendMessage("All visible players have been sent to you.");
					return true;
				}else{
					if(!$issuer->hasPermission("lagfixer.show.player")){
						$issuer->sendMessage("You don't have permission to show an invisible player.");
						return false;
					}
					$player = $this->plugin->getServer()->getPlayer($args[0]);
					if($player instanceof Player and $player->getID() !== $issuer->getID()){
						$player->spawnTo($issuer);
						return true;
					}
				}
			}
			$issuer->sendMessage("Wrong usage: Argument 1 must be a player name or part of it.");
			$issuer->sendMessage("Usage: " . $this->getUsage());
			return false;
		}
		$issuer->sendMessage("Please run this command in-game. You shouldn't see any graphical players here.");
		return false;
	}

	public function getPlugin(){
		return $this->plugin;
	}
}
