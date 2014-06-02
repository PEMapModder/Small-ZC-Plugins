<?php

namespace pemapmodder\pluginloader;



abstract class Plugin implements pocketmine\plugin\Plugin{
	protected $enabled = false;
	public abstract function onLoad();
	public abstract function init();
	public abstract function onDisable();
	public abstract function getFile();
	public function onEnable(){
		$this->enabled = true;
		$this->init();
	}
	public function isEnabled(){
		return $this->enabled;
	}
	public function isDisabled(){
		return !$this->enabled;
	}
	public function getDataFolder
}
