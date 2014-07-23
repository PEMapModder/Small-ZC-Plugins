<?php

namespace pemapmodder\inventorygui;

use pocketmine\scheduler\PluginTask;

class InitTask extends PluginTask{
	/** @var InventoryGUI */
	protected $owner;
	public function __construct(InventoryGUI $main){
		$this->owner = $main;
	}
	public function onRun($t){
		$this->owner->initialize();
	}
}
