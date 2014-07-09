<?php

namespace pemapmodder\numranks;

use pocketmine\event\Listener;
use pocketmine\permission\Permission;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\Config;

class Main extends PluginBase implements Listener{
	/** @var Config */
	private $ranks, $genConfig, $perms;
	private $tmpPermTree = [];
	public function onEnable(){
		@mkdir($this->getDataFolder());
		@mkdir($this->getDataFolder()."players/");
		$this->initConfigs();
		$this->getServer()->getPluginManager()->registerEvents($this, $this);
	}
	public function initConfigs(){
		// ranks
		$df = $this->getDataFolder();
		if(is_file($df."rank-names.yml")){
			rename($df."ranks-names.yml", $df."ranks.yml");
		}
		if(is_file($df."rank-names.txt")){
			rename($df."rank-names.txt", $df."ranks.txt");
		}
		if(!is_file($df."ranks.txt") and !is_file($df."ranks.yml")){
			stream_copy_to_stream($this->getResource("ranks.yml"), fopen($df."ranks.yml", "wt"));
		}
		$this->ranks = new Config(is_file($df."ranks.txt") ? $df."ranks.txt":$df."ranks.yml", Config::YAML);
		$this->genConfig = new Config(is_file($df."config.txt") ? $df."config.txt":$df."config.yml", Config::YAML);
		$permissions = $this->getServer()->getPluginManager()->getDefaultPermissions(true);
		var_dump($permissions);
		if($this->getConfig()->get("recursive-mapping")){
			$permissions = $this->getServer()->getPluginManager()->getPermissions();
			$read = [];
			$parents = [];
			foreach($permissions as $perm){
				foreach($perm->getChildren() as $child){
					$key = array_search($child, $parents);
					if($key !== false){
						unset($parents[$key]);
					}
					if(!in_array($child, $read)){
						$read[] = $child;
					}
				}
				if(!in_array($perm->getName(), $read)){
					$read[] = $perm->getName();
					$parents[] = $perm->getName();
				}
			}
			unset($read);
		}
		else{
			$parents = [];
			foreach($this->getServer()->getPluginManager()->getPermissions() as $perm){
				if(strpos($perm->getName(), ".") === false){ // @shoghicp said I could assume children always have a period and parents always don't :P
					$parents[] = $perm->getName();
				}
			}
		}
		var_dump($parents);
		$data = [];
		$this->fillPermTree($parents);
	}
	private function fillPermTree($permissions, $path = ""){
		eval("\$this->tmpPermTree".$path." = [];");
		eval("\$this->tmpPermTree".$path."");

	}
}
