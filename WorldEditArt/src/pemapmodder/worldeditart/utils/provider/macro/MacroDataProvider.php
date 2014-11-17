<?php

namespace pemapmodder\worldeditart\utils\provider\macro;

use pemapmodder\worldeditart\WorldEditArt;
use pemapmodder\worldeditart\utils\provider\DataProvider;

abstract class MacroDataProvider implements DataProvider{
	private $main;
	public function __construct(WorldEditArt $main){
		$this->main = $main;
	}
	/**
	 * @return WorldEditArt
	 */
	public function getMain(){
		return $this->main;
	}
}
