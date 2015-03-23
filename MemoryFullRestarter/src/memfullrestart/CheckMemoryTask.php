<?php

namespace memfullrestart;

use pocketmine\scheduler\CallbackTask;
use pocketmine\scheduler\PluginTask;

class CheckMemoryTask extends PluginTask{
	public function onRun($t){
		$mem = memory_get_usage(true);
		/** @var MemFullRestarter $main */
		$main = $this->owner;
		if($main->mem <= $mem){
			$main->getServer()->broadcastMessage("Server is overloaded! Restarting server in 5 seconds...");
			$main->getServer()->getScheduler()->scheduleDelayedTask(new CallbackTask(array($this, "stop")), 100);
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
