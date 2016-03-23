<?php

namespace chatchannels;

use chatchannels\cmds\ForceRankCommand;
use pocketmine\Player;
use pocketmine\plugin\PluginBase;
use pocketmine\Server;

class ChatChannels extends PluginBase{
	/** @var Configuration */
	private $configuration;
	/** @var ConsoleSubscriber */
	private $console;
	/** @var ChannelManager */
	private $chanMgr;
	/** @var PrefixAPI */
	private $prefixes;
	/** @var SessionControl */
	private $sessions;

	public function onEnable(){
		$this->saveDefaultConfig();
		$this->configuration = new Configuration($this->getConfig());
		$this->console = new ConsoleSubscriber($this, $this->configuration->getConsoleName());
		$this->chanMgr = new ChannelManager($this);
		$this->chanMgr->addChannel($this->configuration->getDefaultChannel(), $this->console, true); // must be free join
		$this->prefixes = new PrefixAPI($this);
		$this->getServer()->getPluginManager()->registerEvents($this->sessions = new SessionControl($this), $this);
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
		return $this->sessions->getSession($sender);
	}

	public function getDefaultChannel(){
		return $this->chanMgr->getChannel($this->configuration->getDefaultChannel());
	}

	/**
	 * @param Server $server
	 *
	 * @return self
	 */
	public static function getInstance(Server $server){
		return $server->getPluginManager()->getPlugin("ChatChannels");
	}
}
