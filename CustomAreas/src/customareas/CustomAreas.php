<?php

namespace customareas;

use pocketmine\event\Listener;
use pocketmine\plugin\PluginBase;

class CustomAreas extends PluginBase implements Listener{
	public function onEnable(){
		$this->saveDefaultConfig();

	}
}
