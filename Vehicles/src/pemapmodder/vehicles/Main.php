<?php

namespace pemapmodder\vehicles;

use pocketmine\Player;
use pocketmine\command\Command;
use pocketmine\command\CommandSender as Issuer;
use pocketmine\event\Listener;
use pocketmine\plugin\PluginBase as Base;

class Main extends Base implements Listener{
	public function onEnable(){
		$this->getServer()->getPluginManager()->registerEvents($this, $this);
	}
}
