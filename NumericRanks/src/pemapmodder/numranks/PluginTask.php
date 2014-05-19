<?php

namespace pemapmodder\numranks;

use pocketmine\scheduler\PluginTask as ParentClass;

class PluginTask extends ParentClass{
	public function __construct(Main $main){
		parent::__construct($main);
	}
	public function onRun($ticks){
		$this->getPlugin()->onRun($ticks % 20);
	}
}
