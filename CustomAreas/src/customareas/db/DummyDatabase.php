<?php

namespace customareas\db;

use customareas\area\Area;

class DummyDatabase extends Database{
	/** @var Area[] */
	private $areas = [];

	public function init($args){
	}

	public function close(){
	}

	public function getName(){
		return "dummy";
	}

	public function addArea(Area $area){
		$this->areas[$area->getName()] = $area;
	}

	public function rmArea(Area $area){
		unset($this->areas[$area->getName()]);
	}

	public function getArea($name){
		return isset($this->areas[$name]) ? $this->areas[$name] : null;
	}

	public function getAreas(){
		return $this->areas;
	}
}
