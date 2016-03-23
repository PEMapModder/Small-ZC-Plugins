<?php

namespace pemapmodder\ircbridge\bridge\protocol;

class PassCommand extends Command{
	public static $name = "PASS";
	public $pass;

	protected function init($args, $prefix){
		list($this->pass) = $args;
	}
}
