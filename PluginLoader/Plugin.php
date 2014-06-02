<?php

namespace PEMapModder\PluginLoader;

use pocketmine\plugin\PluginBase as PMPB;
use pocketmine\utils\TextFormat as Colors;

class MainPlugin extends PMPB{
	public static $instance;
	public static function getInstance(){
		return self::$instance;
	}
	public function onLoad(){
		if(!$this->getServer()->getPluginManager()->registerInterface("PEMapModder\\PluginLoader\\MPluginLoader")){
			console(Colors::RED."[ERROR] Failed loading PEMapModder's plugin loader!");
		}
		else console(Colors::GREEN."[INFO] Succeeded loading PEMapModder's plugin loader!");
		
		console(($this->getServer()->getPluginManager()->registerInterface("pemapmodder\pluginloader\\YamlPluginLoader") ? "[INFO] Succeeded":"[ERROR] Failed")." loading YamlPluginLoader!");
	}
}
