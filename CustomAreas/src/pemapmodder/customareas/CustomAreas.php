<?php

namespace pemapmodder\customareas;

use pocketmine\plugin\PluginBase;
use pocketmine\utils\Config;

class CustomAreas extends PluginBase{
	private $areas = [];
	private $save;
	public function onEnable(){
		@mkdir($this->getDataFolder());
		$this->save = $areas = new Config($this->getDataFolder()."areas.json", Config::JSON);

	}
	public function onDisable(){
		$areas = [];
		foreach($this->areas as $area){
			// TODO
		}
	}
}
