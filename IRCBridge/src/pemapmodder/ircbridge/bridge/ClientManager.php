<?php

namespace pemapmodder\ircbridge\bridge;

use pemapmodder\ircbridge\bridge\protocol\IRCLine;
use pemapmodder\ircbridge\IRCBridge;

class ClientManager{
	/** @var IRCBridge */
	private $main;
	/** @var Buffer */
	private $buffer;
	/** @var IRCSession[] */
	private $sessions = [];

	public function __construct(IRCBridge $main){
		$this->main = $main;
		$this->buffer = new Buffer;
	}

	/**
	 * @return Buffer
	 */
	public function getBuffer(){
		return $this->buffer;
	}

	public function tick(){
		while($this->buffer->hasMoreRead()){
			$line = IRCLine::parseInternalLine($this->buffer->nextRead(), $signal, $client);
			switch($signal){
				case IRCLine::SIGNAL_OPEN_SESSION:
					$this->sessions[$line] = new IRCSession($line, $this->main);
					break;
				case IRCLine::SIGNAL_CLOSE_SESSION:
					if(isset($this->sessions[$line])){
						$this->sessions[$line]->finalize();
						unset($this->sessions[$line]);
					}
					break;
				case IRCLine::SIGNAL_STD_LINE:
					if(isset($this->sessions[$client])){
						$this->sessions[$client]->handleLine(new IRCLine($line));
					}
			}
		}
	}
}
