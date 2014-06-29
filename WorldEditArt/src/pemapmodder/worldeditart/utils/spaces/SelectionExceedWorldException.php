<?php

namespace pemapmodder\worldeditart\utils\spaces;

class SelectionExceedWorldException extends \Exception{
	public function __construct($class){
		parent::__construct("Unexpected parameters passed to $class such that a selection that is larger than the world has been reached. This is NOT a critical error. This will NOT affect your world.");
	}
}
