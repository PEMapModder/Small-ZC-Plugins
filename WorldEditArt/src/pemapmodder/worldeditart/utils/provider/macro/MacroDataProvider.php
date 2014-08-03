<?php

namespace pemapmodder\worldeditart\utils\provider\macro;

use pemapmodder\worldeditart\Main;
use pemapmodder\worldeditart\utils\provider\DataProvider;

abstract class MacroDataProvider implements DataProvider{
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
