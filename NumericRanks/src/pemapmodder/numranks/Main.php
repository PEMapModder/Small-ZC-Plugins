<?php

namespace pempamodder\numranks;

use pocketmine\utils\Config;
use pocketmine\event\Listener;
use pocketmine\plugin\PluginBase;

class Main extends PluginBase implements Listener{
	const MAGIC_PREFIX = "\0xffNUMRANK";
	const MAGIC_SUFFIX = "FINAL\0x00\0xff\0x00"; // these are necessary to ensure that the writing of database is not corrupted
	public $ranks;
	public $opts;
	const S_CONFIG;
	public function onEnable(){
		$this->getServer()->getPluginManager()->registerEvents($this, $this);
		$this->scheduler = new PluginTask($this);
		$this->getServer()->getScheduler()->scheduleDelayedTask($this->scheduler, self::S_CONFIG);
	}
	public function onRun($ticks){
		switch($ticks % 20){
			case self::S_CONFIG:
				$this->initConfigs();
				
				break;
		}
	}
	private function initConfigs(){
		$this->opts = new Config($this->getDataFolder()."options.yml", Config::YAML, array(
		));
		$perms = $this->getServer()->getPluginManager()->getPermissions();
	}
	private function savePlayers($silent = false){
		if(isset($this->currentTask) and $this->currentTask instanceof AsyncTask){
			trigger_error("NumericRanks cannot start a new InputTask: an ".$this->getSimpleClass($this->currentTask)." is in progress.", E_USER_NOTICE);
		}
		if(!$silent){
			$this->getLogger()->log("[INFO] NumericRanks player database loading-in-progress asynchronously.");
		}
		$this->currentTask = new OutputTask($silent, $this->playersPath);
		$this->getServer()->getScheduler()->scheduleAsyncTask($this->currentTask);
	}
	private function loadPlayers($silent = false){
		if(isset($this->currentTask) and $this->currentTask instanceof AsyncTask){
			trigger_error("NumericRanks cannot start a new InputTask: an ".$this->getSimpleClass($this->currentTask)." is in progress.", E_USER_NOTICE);
		}
		if(!$silent){
			$this->getLogger()->log("[INFO] NumericRanks player database loading-in-progress asynchronously.");
		}
		$this->currentTask = new InputTask($silent, $this->playersPath);
		$this->getServer()->getScheduler()->scheduleAsyncTask($this->currentTask);
	}
	private function getSimpleClass($object){
		if(is_array($object)){
			return "Array";
		}
		if(!is_object($object)){
			return (string) $object;
		}
		$class = get_class($object);
		$slices = explode("\\", $class);
		return array_slice($slices, -1)[0];
	}
	public static function get(){
		return Server::getInstance()->getPluginManager()->getPlugin("NumericRanks");
	}
}
