<?php

namespace pemapmodder\pocketenchant;

use pocketmine\entity\Entity;
use pocketmine\plugin\PluginBase;

class Main extends PluginBase{
	/** @var bool|int */
	public static $EID = false;
	public function onEnable(){
		self::$EID = Entity::$entityCount++;
		$this->saveResource("config.yml");
		$this->saveResource("items.dat");
		$this->saveResource("tables.dat");
		$this->database = new Database($this);
	}
	public function onDisable(){
		$this->getLogger()->info("Deleting all enchantment table shadows");
	}
}
