<?php

namespace pemapmodder\lagfixer;

use pocketmine\plugin\PluginBase;

class LagFixer extends PluginBase{
	public function onEnable(){
		$this->getServer()->getCommandMap()->registerAll("lagfixer", [
			new ShowCommand($this),
			new HealthCommand($this)
		]);
	}
}
