<?php

namespace memfullrestart;

use pocketmine\plugin\PluginBase;

class MemFullRestarter extends PluginBase{
	public $mem;
	/** @var \mysqli|null */
	public $mysqli = null;
	public function onEnable(){
		$this->saveDefaultConfig();
		$this->getServer()->getScheduler()->scheduleDelayedRepeatingTask(new CheckMemoryTask($this), 600, 600);
		$config = $this->getConfig();
		$this->mem = self::return_bytes($config->get("limit"));
		if($config->get("mysql")){
			$this->mysqli = new \mysqli($config->get("host"), $config->get("user"), $config->get("pass"), $config->get("schema"), $config->get("port"));
			$this->mysqli->query("CREATE TABLE IF NOT EXISTS lastping (serverid INT NOT NULL, pid VARCHAR(30) NOT NULL, timestamp INT NOT NULL)");
			$this->getLogger()->info("Refreshing lastping status");
			$serverid = $config->get("serverid");
			$this->mysqli->query("INSERT INTO lastping (serverid, pid, timestamp) VALUES ($serverid, '0', 0) ON DUPLICATE KEY UPDATE pid='0',timestamp=0");
		}
	}
	public function onDisable(){
		$mysql = $this->mysqli;
		if($mysql !== null){
			$config = $this->getConfig();
			$serverid = $config->get("serverid");
			$pid = getmypid();
			$this->getLogger()->notice("Declaring server stop");
			$mysql->query("UPDATE lastping SET pid=$pid,timestamp=unix_timestamp() WHERE serverid=$serverid");
		}
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
