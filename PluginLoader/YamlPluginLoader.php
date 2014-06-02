<?php

namespace pemapmodder\pluginloader;

use pocketmine\Server;
use pocketmine\plugin\PluginLoader as ParentPluginLoader;
use pocketmine\plugin\Plugin;

class YamlPluginLoader implements ParentPluginLoader{
	public function __construct(Server $s){}
	public function getPluginFilters(){
		return "/\\.yml\$/i";
	}
	public function loadPlugin($file){
		return new YamlPluginBase(file_get_contents($file));
	}
	public function getPluginDescription($file){
		return null;
	}
	public function enablePlugin(Plugin $plugin){
		if($plugin instanceof YamlPluginBase)
			$plugin->enable();
	}
	public function disablePlugin(Plugin $plugin){
		if($plugin instanceof YamlPluginBase)
			$plugim->disable();
	}
}
