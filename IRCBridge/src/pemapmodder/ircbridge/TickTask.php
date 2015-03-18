<?php

namespace pemapmodder\ircbridge;

use pocketmine\scheduler\PluginTask;

class TickTask extends PluginTask{
	public function onRun($t){
		/** @var IRCBridge $owner */
		$owner = $this->getOwner();
		$owner->getManager()->tick();
	}
}
