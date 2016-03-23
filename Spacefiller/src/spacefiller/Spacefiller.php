<?php

/**
 * Small-ZC-Plugins
 * Copyright (C) 2015 PEMapModder
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License along
 * with this program; if not, write to the Free Software Foundation, Inc.,
 * 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301 USA.
 */

namespace spacefiller;

use pocketmine\event\EventPriority;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerChatEvent;
use pocketmine\plugin\MethodEventExecutor;
use pocketmine\plugin\PluginBase;

class Spacefiller extends PluginBase implements Listener{
	/** @var Rule[] */
	private $rules = [];

	public function onEnable(){
		$this->saveDefaultConfig();
		$this->rules = array_map(function ($config){
			return new Rule($config["from"], $config["to"], isset($config["limit"]) ? $config["limit"] : -1);
		}, $this->getConfig()->get("rules"));
		$priorityString = $this->getConfig()->get("process-priority", "LOWEST");
		if(defined($name = EventPriority::class . "::" . $priorityString)){
			$priority = constant($name);
		}else{
			$this->getLogger()->warning("Unknown priority $priorityString, assuming LOWEST");
			$priority = EventPriority::LOWEST;
		}
		$this->getServer()->getPluginManager()->registerEvent(PlayerChatEvent::class, $this, $priority, new MethodEventExecutor("onChat"), $this, true);
	}

	public function onChat(PlayerChatEvent $event){
		$msg = $event->getMessage();
		foreach($this->rules as $rule){
			$rule->process($msg);
		}
		$event->setMessage($msg);
	}
}
