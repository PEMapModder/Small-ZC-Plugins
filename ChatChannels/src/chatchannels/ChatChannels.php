<?php

namespace chatchannels;

use pocketmine\plugin\PluginBase;
use pocketmine\Server;

class ChatChannels extends PluginBase{
	/** @var ChannelManager */
	private $chanMgr;
	/** @var PrefixAPI */
	private $prefixes;
	/** @var EventListener */
	private $eventListener;
	public function onEnable(){
		$this->chanMgr = new ChannelManager($this);
		$this->prefixes = new PrefixAPI($this);
		$this->getServer()->getPluginManager()->registerEvents($this->eventListener = new EventListener($this), $this);
	}
	public function getPrefixAPI(){
		return $this->prefixes;
	}
	public function getChannelManager(){
		return $this->chanMgr;
	}
	/**
	 * @param Server $server
	 * @return self
	 */
	public static function getInstance(Server $server){
		return $server->getPluginManager()->getPlugin("ChatChannels");
	}
}
