<?php

namespace pemapmodder\worldeditart\utils\macro;

use pemapmodder\worldeditart\WorldEditArt;
use pocketmine\level\Position;
use pocketmine\scheduler\PluginTask;

class MacroOperationTask extends PluginTask{
	/** @var MacroOperation */
	private $op;
	/** @var Position */
	private $anchor;
	public function __construct(WorldEditArt $main, MacroOperation $op, Position $anchor){
		parent::__construct($main);
		$this->op = $op;
		$this->anchor = $anchor;
	}
	public function onRun($ticks){
		$this->op->operate($this->anchor);
	}
}
