<?php

namespace pemapmodder\worldeditart\utils\provider\player;

use pemapmodder\worldeditart\Main;
use pemapmodder\worldeditart\utils\provider\DataProvider;

abstract class PlayerDataProvider implements DataProvider{
	private $main;
	public function __construct(Main $main){
		$this->main = $main;
	}
	public function offsetExists($name){
		return true;
	}
	/**
	 * @return Main
	 */
	public function getMain(){
		return $this->main;
	}
}
