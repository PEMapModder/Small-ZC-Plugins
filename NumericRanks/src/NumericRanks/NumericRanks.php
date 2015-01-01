<?php

namespace NumericRanks;

use pocketmine\plugin\PluginBase;

class NumericRanks extends PluginBase
{
	private $cfg;
	
	public function onEnable()
	{
		$this->loadConfig();
	}
	
	public function loadConfig()
	{
		$this->saveDefaultConfig();
		
		$this->cfg = $this->getConfig();
	}
}