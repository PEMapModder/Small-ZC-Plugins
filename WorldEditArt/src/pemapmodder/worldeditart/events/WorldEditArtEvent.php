<?php

namespace pemapmodder\worldeditart\events;

use pemapmodder\worldeditart\WorldEditArt;
use pocketmine\event\Event;

abstract class WorldEditArtEvent extends Event{
	private $main;

	protected function __construct(WorldEditArt $main){
		$this->main = $main;
	}

	public function getPlugin(){
		return $this->main;
	}
}
