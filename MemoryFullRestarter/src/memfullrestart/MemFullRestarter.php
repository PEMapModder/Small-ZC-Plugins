<?php

namespace memfullrestart;

use pocketmine\plugin\PluginBase;

class MemFullRestarter extends PluginBase{
	public $mem;
	public function onEnable(){
		$this->saveDefaultConfig();
		$this->getServer()->getScheduler()->scheduleDelayedRepeatingTask(new CheckMemoryTask($this), 100, 100);
		$this->mem = self::return_bytes($this->getConfig()->get("limit"));
	}
	/**
	 * @param string $val
	 * @return int
	 */
	public static function return_bytes($val){
		$val = trim($val);
		$value = (int) $val;
		switch(strtolower(substr($val, -1))){
			/** @noinspection PhpMissingBreakStatementInspection */
			case "g":
			$value *= 1024;
			/** @noinspection PhpMissingBreakStatementInspection */
			case "m":
				$value *= 1024;
			case "k":
				$value *= 1024;
				break;
		}
		return $value;
	}
}
