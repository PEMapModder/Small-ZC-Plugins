<?php

namespace pemapmodder\worldeditart\events;

use pemapmodder\worldeditart\Main;
use pocketmine\event\Event;

abstract class WorldEditArtEvent extends Event{
	private $main;
	protected function __construct(Main $main){
		$class = new \ReflectionClass($this);
		if(!isset($class->getStaticProperties()["handlerList"])){
			throw new \RuntimeException("Field \$handlerList not declared in class ".$class->getShortName());
		}
		$this->main = $main;
	}
	public function getPlugin(){
		return $this->main;
	}
}
