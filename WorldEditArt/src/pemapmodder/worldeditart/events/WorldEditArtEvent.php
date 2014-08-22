<?php

namespace pemapmodder\worldeditart\events;

use pemapmodder\worldeditart\Main;
use pocketmine\event\Event;

abstract class WorldEditArtEvent extends Event{
	private $main;
	protected function __construct(Main $main){
		$this->main = $main;
	}
	public function getPlugin(){
		return $this->main;
	}
}
