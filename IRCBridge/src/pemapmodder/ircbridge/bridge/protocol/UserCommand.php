<?php

namespace pemapmodder\ircbridge\bridge\protocol;

class UserCommand extends Command{
	public static $name = "USER";
	public $user, $mode, $unused, $realname;
	protected function init($args, $prefix){
		list($this->user, $mode, $this->unused, $this->realname) = $args;
		$this->mode = (int) $mode;
	}
}
