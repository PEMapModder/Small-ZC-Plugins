<?php

namespace ConsolePush;

use pocketmine\event\Listener;
use pocketmine\event\server\ServerCommandEvent;
use pocketmine\plugin\PluginBase;

class ConsolePush extends PluginBase implements Listener{
	const CHAR_CANCEL = "c";
	const CHAR_PUSH = "p";
	const CHAR_RTRIM = "t";
	const CHAR_NORMAL = "n";
	public function onEnable(){
		$this->getServer()->getPluginManager()->registerEvents($this, $this);
	}
	public function onConsoleCmd(ServerCommandEvent $event){
		$msg = $event->getCommand();
		if(preg_match('/^(.*)\\\\([a-z])$/', $msg, $match)){
			$char = $match[2];
			switch($char){
				case self::CHAR_NORMAL:
					$event->setCommand($match[1]);
					break;
				case self::CHAR_RTRIM:
					$event->setCommand(rtrim($match[1]));
					break;
				case self::CHAR_CANCEL:
					$event->setCancelled();
					break;
				case self::CHAR_PUSH:
					$event->setCancelled();
					echo $match[1];
					break;
			}
		}
	}
}
