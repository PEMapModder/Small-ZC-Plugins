<?php

namespace memfullrestart;

use pocketmine\scheduler\CallbackTask;
use pocketmine\scheduler\PluginTask;

class CheckMemoryTask extends PluginTask{
	private $cancelled = 0;

	public function onRun($t){
		if($this->cancelled > 0){
			return;
		}
		/** @var MemFullRestarter $main */
		$main = $this->owner;
		if($main->mysqli !== null){
			$sid = $main->getConfig()->get("serverid");
			$main->mysqli->query("UPDATE lastping SET timestamp=unix_timestamp() WHERE serverid=$sid");
		}
		$mem = memory_get_usage(true);
		if($main->mem <= $mem){
			$main->getServer()->broadcastMessage("Server is overloaded! Restarting server in 5 seconds...");
			$main->getServer()->getScheduler()->scheduleDelayedTask(new CallbackTask([$this, "stop"]), 100);
			$main->getServer()->getScheduler()->cancelTask($this->getTaskId());
		}
	}

	public function stop(){
		foreach($this->owner->getServer()->getOnlinePlayers() as $p){
			$p->close("Server restarted due to memory overloaded.");
		}
		$this->owner->getServer()->shutdown();
	}
}
