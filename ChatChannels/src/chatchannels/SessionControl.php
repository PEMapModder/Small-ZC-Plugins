<?php

namespace chatchannels;

use pocketmine\event\Listener;
use pocketmine\event\player\PlayerChatEvent;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\event\plugin\PluginDisableEvent;
use pocketmine\Player;

class SessionControl implements Listener{
	private $plugin;
	/** @var PlayerSubscriber[] */
	private $playerSubs = [];
	public function __construct(ChatChannels $plugin){
		$this->plugin = $plugin;
	}
	public function onPluginDisabled(PluginDisableEvent $e){
		$this->plugin->getPrefixAPI()->recalculateAll($e->getPlugin());
	}
	public function onJoin(PlayerJoinEvent $event){
		$player = $event->getPlayer();
		$this->playerSubs[$player->getId()] = new PlayerSubscriber($this->plugin, $player);
	}
	/**
	 * @param Player $player
	 * @return PlayerSubscriber
	 */
	public function getSession(Player $player){
		return $this->playerSubs[$player->getId()];
	}
	public function onQuit(PlayerQuitEvent $event){
		$p = $event->getPlayer();
		if(isset($this->playerSubs[$p->getId()])){
			$this->playerSubs[$p->getId()]->release();
			unset($this->playerSubs[$p->getId()]);
		}
	}
	public function onChat(PlayerChatEvent $event){
		$event->setCancelled();
		$player = $event->getPlayer();
		$sub = $this->playerSubs[$player->getID()];
		$sub->onChatEvent($event->getMessage());
	}
	public function setMute(Player $player, $muted){
		$sub = $this->playerSubs[$player->getId()];
		$original = $sub->muted;
		$sub->muted = $muted;
		return $original;
	}
}
