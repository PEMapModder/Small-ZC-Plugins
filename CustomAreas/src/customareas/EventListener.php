<?php

namespace customareas;

use pocketmine\event\level\LevelLoadEvent;
use pocketmine\event\level\LevelUnloadEvent;
use pocketmine\event\Listener;

class EventListener implements Listener{
	private $plugin;
	public function __construct(CustomAreas $plugin){
		$this->plugin = $plugin;
		$plugin->getServer()->getPluginManager()->registerEvents($this, $plugin);
	}
	public function onLevelLoaded(LevelLoadEvent $event){
		$this->plugin->loadAreas($event->getLevel());
	}
	public function onLevelUnloaded(LevelUnloadEvent $event){
		$this->plugin->unloadAreas($event->getLevel());
	}
}
