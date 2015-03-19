<?php

namespace pemapmodder\ircbridge\bridge\protocol;

class NickCommand extends Command{
	public static $name = "NICK";
	/** @var string */
	public $user = false, $nick;
	protected function init($args, $prefix){
		$this->nick = $args[0];
	}
	public function encode(){
		return ":$this->user NICK $this->nick";
	}
}
