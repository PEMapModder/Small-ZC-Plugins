<?php

namespace pemapmodder\worldeditart\utils\macro;

use pemapmodder\worldeditart\Main;
use pocketmine\level\Position;
use pocketmine\scheduler\PluginTask;

class MacroOperationTask extends PluginTask{
	/** @var MacroOperation */
	private $op;
	/** @var Position */
	private $anchor;
	public function __construct(Main $main, MacroOperation $op, Position $anchor){
		parent::__construct($main);
		$this->op = $op;
		$this->anchor = $anchor;
	}
	public function onRun($ticks){
		$this->op->operate($this->anchor);
	}
}
