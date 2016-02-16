<?php

/*
 * Small-ZC-Plugins
 *
 * Copyright (C) 2016 PEMapModder
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * @author PEMapModder
 */

namespace BroomArrow;

use pocketmine\Player;

class BroomSession{
	/** @var BroomArrow */
	private $main;
	/** @var Player */
	private $player;

	private $isOnBroom;
	private $currentBroomId;
	private $brooms;

	public function __construct(BroomArrow $main, Player $player){
		$this->main = $main;
		$this->player = $player;
	}

	public function getMain(){
		return $this->main;
	}

	public function getPlayer(){
		return $this->player;
	}


}
