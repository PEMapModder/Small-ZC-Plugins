<?php

namespace pemapmodder\worldeditart\tasks;

use pemapmodder\worldeditart\Main;
use pemapmodder\worldeditart\utils\provider\Cached;
use pocketmine\scheduler\PluginTask;

class GarbageCollectionTask extends PluginTask{
	/** @var Cached */
	private $cached;
	/** @var number */
	private $expiry;
	public function __construct(Main $main, Cached $cached){
		parent::__construct($main);
		$this->cached = $cached;
		$this->expiry = $main->getConfig()->get("data providers")["cache time"];
	}
	public function onRun($ticks){
		$this->cached->collectGarbage($this->expiry);
	}
}
