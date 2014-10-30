<?php

namespace chatchannels;

use pocketmine\event\Listener;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\event\plugin\PluginDisableEvent;
use pocketmine\Player;

class EventListener implements Listener{
	private $plugin;
	/** @var PlayerSubscriber[] */
	private $playerSubs = [];
	public function __construct(ChatChannels $lugin){
		$this->plugin = $lugin;
	}
	public function onPluginDisabled(PluginDisableEvent $e){
		$this->plugin->getPrefixAPI()->recalculateAll($e->getPlugin());
	}
	public function onJoin(PlayerJoinEvent $event){
		$player = $event->getPlayer();
		$this->playerSubs[$player->getId()] = new PlayerSubscriber($player);
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
}
