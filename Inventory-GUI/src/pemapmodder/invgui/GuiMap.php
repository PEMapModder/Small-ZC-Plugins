<?php

namespace pemapmodder\invgui;

class GuiMap{
	public $map = array();
	public $priority = array();
	public $submaps = array();
	public function __construct(){
	}
	public function getSubmap($key){
		$ret = $this;
		foreach($key as $sub)
			$ret = $ret->getMap($sub);
		return $ret;
	}
	public function getMap($key){
		if(!isset($this->submaps[$key]))
			$this->submaps[$key] = new self();
		return $this->submaps[$key];
	}
}
