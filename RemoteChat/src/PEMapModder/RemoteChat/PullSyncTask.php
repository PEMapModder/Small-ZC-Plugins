<?php

namespace PEMapModder\RemoteChat;

use pocketmine\scheduler\PluginTask;

class PullSyncTask extends PluginTask{
	public function onRun($t){
		/** @var RemoteChat $main */
		$main = $this->getOwner();
		$main->tick();
	}
}
