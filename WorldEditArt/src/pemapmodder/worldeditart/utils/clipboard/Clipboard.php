<?php

namespace pemapmodder\worldeditart\utils\clipboard;

class Clipboard{
	const GLOBAL_OWNER = null;
	private $owner;
	public function __construct($owner){
		$this->owner = $owner;
	}

}
