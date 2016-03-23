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
	// since we only have one console, one cache is enough, no need for session management :)
	public $currentLine;

	public function onEnable(){
		$this->getServer()->getPluginManager()->registerEvents($this, $this);
	}

	public function onConsoleCmd(ServerCommandEvent $event){
		if(isset($this->currentLine)){
			$event->setCommand($this->currentLine . $event->getCommand());
			unset($this->currentLine);
		}
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
					$this->simulateInput($match[1]);
					break;
			}
		}
	}

	public function simulateInput($input){
		echo $input;
		$this->currentLine = $input;
	}
}
