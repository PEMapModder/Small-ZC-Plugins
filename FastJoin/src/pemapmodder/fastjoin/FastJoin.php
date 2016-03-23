<?php

namespace pemapmodder\fastjoin;

use pocketmine\event\Listener;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerLoginEvent;
use pocketmine\level\Position;
use pocketmine\plugin\PluginBase;

class FastJoin extends PluginBase implements Listener{
	private $table = [];

	public function onEnable(){
		$path = $this->getServer()->getDataPath() . "/worlds/.fastjoin/";
		FlatProvider::generate($path, ".fastjoin", 0, "");
		$this->getServer()->loadLevel(".fastjoin");
	}

	public function onPreLogin(PlayerLoginEvent $event){
		$player = $event->getPlayer();
		$this->table[spl_object_hash($player)] = $player->getPosition();
		$player->teleport(new Position(0, 128, 0, $this->getServer()->getLevelByName(".fastjoin")));
	}

	public function onJoin(PlayerJoinEvent $event){
		$player = $event->getPlayer();
		$this->getServer()->getScheduler()->scheduleDelayedTask(new TeleportTask($this, $player, $this->table[spl_object_hash($player)]), 20);
		unset($this->table[spl_object_hash($player)]);
	}
}
