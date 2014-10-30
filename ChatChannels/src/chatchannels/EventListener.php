<?php

namespace chatchannels;

use pocketmine\event\Listener;
use pocketmine\event\plugin\PluginDisableEvent;

class EventListener implements Listener{
	private $plugin;
	public function __construct(ChatChannels $lugin){
		$this->plugin = $lugin;
	}
	public function onPluginDisabled(PluginDisableEvent $e){
		$this->plugin->getPrefixAPI()->recalculateAll($e->getPlugin());
	}
}
