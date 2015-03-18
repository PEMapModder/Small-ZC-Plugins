<?php

namespace pemapmodder\ircbridge\bridge\protocol;

class PassCommand extends Command{
	public $pass;
	public function init($args, $prefix){
		list($this->pass) = $args;
	}
}
