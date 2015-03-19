<?php

namespace pemapmodder\ircbridge\bridge\protocol;

class ModeCommand extends Command{
	public static $name = "MODE";
	public $target;
	public $modes = [];
	protected function init($args, $prefix){
		$this->target = $args[0];
		preg_match_all('/(\+|\-)([A-Za-z]+)/', $args[1], $matches, PREG_SET_ORDER);
		foreach($matches as $match){
			$true = $match[1] === "+";
			foreach(str_split($match[2]) as $char){
				$this->modes[$char] = $true;
			}
		}
	}
}
