<?php

namespace pemapmodder\vehicles;

class VehicleType{
	protected $name, $ids, $class;
	public function __cosntruct($name, array $identifiers, $class){
		if(!is_subclass_of($class, "pemapmodder\\vehicles\\Vehicle")){
			trigger_error("Argument 3 passed to ".get_class()."::__construct() must be class name of pemapmodder\\vehicles\\Vehicle, $class given", E_USER_ERROR);
		}
		$this->name = $name;
		$this->ids = $identifiers;
		$this->class = $class;
	}
	public function getName(){
		return $this->name;
	}
	public function matches(){
		return true;
	}
	public function getClass(){
		return $this->class;
	}
}
