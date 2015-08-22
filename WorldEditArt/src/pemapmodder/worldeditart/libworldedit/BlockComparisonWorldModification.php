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

use pocketmine\block\Block;
use pocketmine\Player;

class BlockComparisonWorldModification extends WorldModification{
	/**
	 * @param Block[][] $replaces
	 */
	public function __construct(array $replaces){
		foreach($replaces as $replace){
			if(!is_array($replace) or count($replace) !== 2){
				throw new \InvalidArgumentException("Argument 1 passed to " . self::class . "::__construct must be array of " . Block::class . "[2], ");
			}
		}
	}
	protected function run(){
		// TODO: Implement run() method.
	}
	protected function undo(){
		// TODO: Implement undo() method.
	}
	/**
	 * @param $players
	 * @return Player|Player[]
	 */
	public function sendChangesTo($players){
		// TODO: Implement sendChangesTo() method.
	}
}
