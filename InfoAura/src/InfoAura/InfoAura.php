<?php

/*
 * InfoAura
 *
 * Copyright (C) 2015 PEMapModder and contributors
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * @author PEMapModder
 */

namespace InfoAura;

use pocketmine\event\Listener;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\level\Position;
use pocketmine\math\Vector3;
use pocketmine\plugin\PluginBase;

class InfoAura extends PluginBase implements Listener{
	public function onEnable(){
		$this->getServer()->getPluginManager()->registerEvents($this, $this);
	}
	public function onInteract(PlayerInteractEvent $event){
		$id = $this->getConfig()->getNested("item.id", 345);
		$damage = $this->getConfig()->getNested("item.damage", null);
		if($event->getItem()->getId() === $id and ($damage === null or $event->getItem()->getDamage() === $damage) and $event->getAction() === PlayerInteractEvent::RIGHT_CLICK_AIR){
			$this->findPlayer($event->getPlayer(), $event->getTouchVector(), $event->getPlayer()->getId());
		}
	}
	public function findPlayer(Position $from, Vector3 $vector, $excludeEid){
		foreach($from->getLevel()->getPlayers() as $player){
			if($player->getId() === $excludeEid){
				continue;
			}
			$diff = $player->subtract($from);
			$distance = $diff->length();
			$norm = $diff->divide($distance);

		}
	}
}
