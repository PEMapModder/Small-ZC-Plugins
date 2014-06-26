<?php

namespace pemapmodder\worldeditart\tasks;

use pemapmodder\worldeditart\Main;
use pemapmodder\worldeditart\utils\spaces\Space;
use pocketmine\scheduler\PluginTask;

class UndoTestTask extends PluginTask{
	private $space;
	public function __construct(Main $main, Space $space){
		parent::__construct($main);
		$this->space = clone $space;
	}
	public function onRun($ticks){
		$this->space->undoLast();
	}
}
