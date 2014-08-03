<?php

namespace pemapmodder\worldeditart\utils\provider\clip;

use pemapmodder\worldeditart\Main;
use pemapmodder\worldeditart\utils\provider\DataProvider;

abstract class ClipboardProvider implements DataProvider{
	private $main;
	public function __construct(Main $main){
		$this->main = $main;
	}
	/**
	 * @return Main
	 */
	public function getMain(){
		return $this->main;
	}
}
