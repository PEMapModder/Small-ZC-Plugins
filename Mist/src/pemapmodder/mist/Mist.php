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

namespace pemapmodder\mist;

use pocketmine\Player;

class Mist{
	private $player;
	private $count;
	/** @var MistySpecter[] */
	private $specters = [];

	public function __construct(Player $player, $count){
		$this->player = $player;
		$this->count = $count;
	}

	public function init(){
		for($i = 0; $i < $this->count; $i++){
			$this->specters[$i] = new MistySpecter($this->player);
		}
	}

	public function tick(){
	}

	/**
	 * @return Player
	 */
	public function getPlayer(){
		return $this->player;
	}

	/**
	 * @return mixed
	 */
	public function getCount(){
		return $this->count;
	}
}
