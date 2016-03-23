<?php

/*
 * Small-ZC-Plugins
 *
 * Copyright (C) 2016 PEMapModder
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * @author PEMapModder
 */

namespace BroomArrow;

use pocketmine\event\Listener;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\Player;
use pocketmine\plugin\PluginBase;

class BroomArrow extends PluginBase implements Listener{
	private $sessions = [];

	public function onEnable(){
		$this->getServer()->getPluginManager()->registerEvents($this, $this);
		foreach($this->getServer()->getOnlinePlayers() as $player){
			$this->openSession($player);
		}
	}

	public function onDisable(){
		foreach($this->getServer()->getOnlinePlayers() as $player){
			$this->closeSession($player);
		}
	}

	public function e_join(PlayerJoinEvent $event){
		$this->openSession($event->getPlayer());
	}

	private function openSession(Player $player){
		$this->sessions[$player->getId()] = new BroomSession($this, $player);
	}

	private function closeSession(Player $player){
	}
}
