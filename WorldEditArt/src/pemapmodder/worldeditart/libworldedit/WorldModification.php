<?php

/*
 * Small-ZC-Plugins
 *
 * Copyright (C) 2015 PEMapModder and contributors
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * @author PEMapModder
 */

namespace pemapmodder\worldeditart\libworldedit;

use pocketmine\Player;

abstract class WorldModification{
	private $executed = false;
	public final function execute(){
		if($this->executed){
			return false;
		}
		$this->executed = true;
		return $this->run();
	}
	protected abstract function run();
	public final function reverse(){
		if(!$this->executed){
			return false;
		}
		$this->executed = false;
		return $this->undo();
	}
	protected abstract function undo();
	/**
	 * @param $players
	 * @return Player|Player[]
	 */
	public abstract function sendChangesTo($players);
}
