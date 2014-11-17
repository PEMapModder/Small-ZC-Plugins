<?php

namespace pemapmodder\worldeditart\tasks;

use pemapmodder\worldeditart\WorldEditArt;
use pemapmodder\worldeditart\utils\spaces\Space;
use pocketmine\scheduler\PluginTask;

class UndoTestTask extends PluginTask{
	/** @var Space */
	private $space;
	public function __construct(WorldEditArt $main, Space $space){
		parent::__construct($main);
		$this->space = clone $space;
	}
	public function onRun($ticks){
//		$this->space->undoLastTest();
	}
	public function onCancel(){
		$this->onRun(0);
	}
}
