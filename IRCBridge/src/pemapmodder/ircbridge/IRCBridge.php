<?php

namespace pemapmodder\ircbridge;

use pemapmodder\ircbridge\bridge\ClientManager;
use pemapmodder\ircbridge\bridge\IRCServer;
use pocketmine\plugin\PluginBase;

class IRCBridge extends PluginBase{
	/** @var ClientManager */
	private $mgr;
	/** @var IRCServer */
	private $thread;
	private $startTime;

	public function onEnable(){
		$this->saveDefaultConfig();
		$this->mgr = new ClientManager($this);
		$this->startTime = microtime(true);
		$this->thread = new IRCServer($this->mgr->getBuffer(), $this->getConfig()->getNested("server.ip", "0.0.0.0"), $this->getConfig()->getNested("server.port", 6667));
	}

	public function onDisable(){
		$this->thread->stop();
	}

	/**
	 * @return ClientManager
	 */
	public function getManager(){
		return $this->mgr;
	}

	public function getCreationTime(){
		return date(DATE_ATOM, $this->startTime);
	}
}
