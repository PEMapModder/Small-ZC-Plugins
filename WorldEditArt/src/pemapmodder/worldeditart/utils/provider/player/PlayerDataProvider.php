<?php

namespace pemapmodder\worldeditart\utils\provider\player;

use pemapmodder\worldeditart\utils\provider\DataProvider;
use pemapmodder\worldeditart\WorldEditArt;

abstract class PlayerDataProvider implements DataProvider{
	private $main;

	public function __construct(WorldEditArt $main){
		$this->main = $main;
	}

	public function offsetExists($name){
		return true;
	}

	/**
	 * @return WorldEditArt
	 */
	public function getMain(){
		return $this->main;
	}
}
