<?php

namespace pemapmodder\ircbridge\bridge\protocol;

class QuitCommand extends Command{
	public static $name = "QUIT";
	public $msg;

	protected function init($args, $prefix){
		$this->msg = $args[0];
	}
}
