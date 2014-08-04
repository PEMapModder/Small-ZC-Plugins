<?php

namespace pemapmodder\mrcchat;

use pocketminw\plugin\PluginBase;

class MRCChat extends PluginBase{
	pricate $prodiver;
	public function onEnable(){
		$this->saveDefaultConfig();
		
	}
}
