<?php

namespace pemapmodder\ircbridge\bridge;

use pemapmodder\ircbridge\bridge\protocol\IRCLine;
use pemapmodder\ircbridge\bridge\protocol\ModeCommand;
use pemapmodder\ircbridge\bridge\protocol\NickCommand;
use pemapmodder\ircbridge\bridge\protocol\PassCommand;
use pemapmodder\ircbridge\bridge\protocol\QuitCommand;
use pemapmodder\ircbridge\bridge\protocol\UserCommand;
use pemapmodder\ircbridge\IRCBridge;

class IRCSession{
	/** @var string */
	private $identifier, $host;
	/** @var IRCBridge */
	private $main;
	/** @var string */
	private $username, $realname;
	private $away, $invisible = false, $wallops = false, $restricted = false, $oper = false, $OperLocal = false, $serverNoticeRecepient = false;
	private $ready = false;

	public function __construct($identifier, IRCBridge $main){
		$this->identifier = $identifier;
		$this->host = strstr($identifier, ":", true);
		$this->main = $main;
	}

	public function handleLine(IRCLine $line){
		try{
			$cmd = $line->getCommand();
		}catch(\Exception $e){
			if(stripos(get_class($e), "outofboundsexception") !== false){
				$this->send("461 {$line->getCmdName()} :Not enough parameters");
			}
			return;
		}
		switch($cmd->getName()){
			case PassCommand::$name:
				return; // ignored
			case NickCommand::$name:
				/** @var NickCommand $cmd */
				$this->send("432 $cmd->nick :Erroneous nickname");
				return;
			case UserCommand::$name:
				/** @var UserCommand $cmd */
				$this->username = $cmd->user;
				$this->realname = $cmd->realname;
				return;
			case ModeCommand::$name:
				// TODO
				return;
			case "SERVICE":
				return;
			case "OPER":
				return;
			case QuitCommand::$name:
				$this->main->getManager()->getBuffer()->addWrite(chr(IRCLine::SIGNAL_CLOSE_SESSION) . $this->identifier);
				return;
		}
	}

	public function ready(){
		$this->send("001 :Welcome to Internet Relay Network $this->username!$this->username@$this->host");
		$name = $this->main->getConfig()->getNested("server.name", "PocketMine IRCBridge Network");
		$version = $this->main->getDescription()->getVersion();
		$this->send("002 :Your host is $name, running version $version");
		$this->send("003 :This server was created {$this->main->getCreationTime()}");
		$this->send("004 :$name $version umode cmode"); // TODO
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

	public function send($line){
		$this->main->getManager()->getBuffer()->addWrite(chr(IRCLine::SIGNAL_STD_LINE) . $this->identifier . " $line");
	}
}
