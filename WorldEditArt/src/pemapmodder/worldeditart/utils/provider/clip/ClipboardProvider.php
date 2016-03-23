<?php

namespace pemapmodder\worldeditart\utils\provider\clip;

use pemapmodder\worldeditart\utils\provider\DataProvider;
use pemapmodder\worldeditart\WorldEditArt;

abstract class ClipboardProvider implements DataProvider{
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
