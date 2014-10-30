<?php

namespace chatchannels;

use chatchannels\cmds\ForceRankCommand;
use pocketmine\Player;
use pocketmine\plugin\PluginBase;
use pocketmine\Server;

class ChatChannels extends PluginBase{
	private $configuration;
	/** @var ConsoleSubscriber */
	private $console;
	/** @var ChannelManager */
	private $chanMgr;
	/** @var PrefixAPI */
	private $prefixes;
	/** @var EventListener */
	private $eventListener;
	public function onEnable(){
		$this->saveDefaultConfig();
		$this->configuration = new Configuration($this->getConfig());
		$this->console = new ConsoleSubscriber($this, $this->configuration->getConsoleName());
		$this->chanMgr = new ChannelManager($this);
		$this->prefixes = new PrefixAPI($this);
		$this->getServer()->getPluginManager()->registerEvents($this->eventListener = new EventListener($this), $this);
		$this->getServer()->getCommandMap()->registerAll("chan", [
			new ForceRankCommand($this, "mod", Channel::MODE_MOD, "fa"),
			new ForceRankCommand($this, "admin", Channel::MODE_ADMIN, "fa"),
		]);
	}
	public function getPrefixAPI(){
		return $this->prefixes;
	}
	public function getChannelManager(){
		return $this->chanMgr;
	}
	public function getConsole(){
		return $this->console;
	}
	public function getPlayerSub(Player $sender){
		return $this->eventListener->getSession($sender);
	}
	/**
	 * @param Server $server
	 * @return self
	 */
	public static function getInstance(Server $server){
		return $server->getPluginManager()->getPlugin("ChatChannels");
	}
}
