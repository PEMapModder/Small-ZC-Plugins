<?php

namespace pemapmodder\worldeditart\utils\provider\player;

class DummyPlayerDataProvider extends PlayerDataProvider{
	public function offsetGet($name){
		return new PlayerData($this->getMain(), $name);
	}

	public function offsetSet($name, $data){
	}

	public function offsetUnset($name){
	}

	public function getName(){
		return "Dummy Player Data Provider";
	}

	public function isAvailable(){
		// return false;
		return true;
	}

	public function close(){
	}
}
