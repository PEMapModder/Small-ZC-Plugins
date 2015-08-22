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
use pocketmine\utils\Random;

class BlockSet{
	/** @var Block[] */
	private $blocks;
	/** @var Random */
	private $random;
	/**
	 * @param Block[] $blocks
	 * @param int $seed default -1
	 */
	public function __construct($blocks, $seed = -1){
		$this->blocks = $blocks;
		$this->random = new Random($seed);
	}
	public function getNext(){
		$this->random->nextRange(0, count($this->blocks) - 1);
	}
}
