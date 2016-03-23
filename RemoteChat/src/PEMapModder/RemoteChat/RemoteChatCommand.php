<?php

namespace PEMapModder\RemoteChat;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\command\PluginIdentifiableCommand;

class RemoteChatCommand extends Command implements PluginIdentifiableCommand{
	private $main;

	public function __construct(RemoteChat $main){
		$this->main = $main;
		$config = $main->getConfig();
		parent::__construct($config->getNested("cmd.name"), $config->getNested("cmd.description", "Send chat to a player on another server with RemoteChat enabled."), $config->getNested("cmd.usage-message"), $config->getNested("cmd.aliases", ["rc"]));
	}

	/**
	 * @return RemoteChat
	 */
	public function getPlugin(){
		return $this->main;
	}

	public function execute(CommandSender $sender, $lbl, array $args){
		if(!isset($args[3])){
			return false;
		}
		// TODO
	}
}
