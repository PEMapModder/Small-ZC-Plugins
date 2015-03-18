<?php

namespace pemapmodder\ircbridge\bridge;

use pemapmodder\ircbridge\bridge\protocol\IRCLine;
use pemapmodder\ircbridge\IRCBridge;

class IRCSession{
	/** @var string */
	private $identifier;
	/** @var IRCBridge */
	private $main;
	private $ready = false;
	public function __construct($identifier, IRCBridge $main){
		$this->identifier = $identifier;
		$this->main = $main;
	}
	public function handleLine(IRCLine $line){
		$formatted =
	}
	public function finalize(){
		// TODO
	}
	/**
	 * @return boolean
	 */
	public function isReady(){
		return $this->ready;
	}
}
