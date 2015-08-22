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

namespace pemapmodder\worldeditart;

use pemapmodder\worldeditart\libworldedit\space\Space;
use pemapmodder\worldeditart\libworldedit\WorldModification;
use pocketmine\level\Position;

abstract class Session{
	/** @var WorldModification[] */
	protected $execQueue = [];
	/** @var int */
	protected $redoPointer = 0;
	/** @var Space */
	protected $selection;
	/**
	 * @return Position
	 */
	public abstract function getPosition();
	/**
	 * @return WorldEditArt
	 */
	public abstract function getMain();
	public function execute(WorldModification $mod){
		if($this->redoPointer !== count($this->execQueue)){
			$this->execQueue = array_slice($this->execQueue, 0, $this->redoPointer);
		}
		$this->execQueue[$this->redoPointer++] = $mod;
		while(count($this->execQueue) > $this->getMain()->getMaxUndoQueue()){
			array_shift($this->execQueue);
		}
	}
	public function undo(){
		if($this->redoPointer === 0){
			return false;
		}
		$this->execQueue[--$this->redoPointer]->reverse();
		return true;
	}
	public function redo(){
		if(!isset($this->execQueue[$this->redoPointer])){
			return false;
		}
		$this->execQueue[$this->redoPointer++]->execute();
		return true;
	}
}
