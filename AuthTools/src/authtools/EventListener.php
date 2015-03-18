<?php

namespace authtools;

use pocketmine\event\Listener;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerQuitEvent;
use SimpleAuth\event\PlayerAuthenticateEvent;
use SimpleAuth\event\PlayerDeauthenticateEvent;

class EventListener implements Listener{
	/** @var AuthTools */
	private $main;
	public function __construct(AuthTools $main){
		$this->main = $main;
	}
	/**
	 * @param PlayerDeauthenticateEvent $event
	 * @priority MONITOR
	 */
	public function onDeauth(PlayerDeauthenticateEvent $event){
		$this->main->closeInternalSession($player = $event->getPlayer());
		$this->main->openInternalSession($player);
	}
	/**
	 * @param PlayerJoinEvent $event
	 * @priority MONITOR
	 */
	public function onJoin(PlayerJoinEvent $event){
		$this->main->openInternalSession($event->getPlayer());
	}
	public function onAuth(PlayerAuthenticateEvent $event){
		$this->main->closeInternalSession($event->getPlayer());
	}
	/**
	 * @param PlayerQuitEvent $event
	 * @priority MONITOR
	 */
	public function onQuit(PlayerQuitEvent $event){
		$this->main->closeInternalSession($event->getPlayer());
	}
}
