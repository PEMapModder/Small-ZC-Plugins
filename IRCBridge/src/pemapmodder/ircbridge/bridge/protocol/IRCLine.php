<?php

namespace pemapmodder\ircbridge\bridge\protocol;

class IRCLine{
	/** @var string */
	private $prefix, $cmd;
	/** @var string[] */
	private $args;
	/** @var bool */
	public static $init = false;
	public function __construct($line){
		if(preg_match("/(:([^ ]+) )?([A-Z0-9]+) (.*)\$/i", trim($line), $matches)){
			list(, , $this->prefix, $this->cmd, $args) = $matches;
			if(($pos = strpos($args, " :")) !== false){
				$this->args = explode(" ", substr($args, 0, $pos));
				$this->args[] = substr($args, $pos + 2);
			}else{
				$this->args = explode(" ", $args);
			}
		}else{
			throw new \UnexpectedValueException("Incorrect IRC syntax");
		}
	}
	public function getPrefix(){
		return $this->prefix;
	}
	public function getCmdName(){
		return $this->cmd;
	}
	public function getCommand(){
		if(!self::$init){
			self::init();
		}
		if(!isset(Command::$cmds[strtoupper($this->cmd)])){
			return null;
		}
		$class = Command::$cmds[strtoupper($this->cmd)];
		/** @var Command $instance */
		$instance = new $class($this);
		return $instance;
	}
	public function getArguments(){
		return $this->args;
	}
	const SIGNAL_OPEN_SESSION = 0;
	const SIGNAL_STD_LINE = 1;
	const SIGNAL_CLOSE_SESSION = 2;
	public static function parseInternalLine($line, &$signal, &$client = false){
		if($line === false){
			return false;
		}
		$signal = ord(substr($line, 0, 1));
		if($signal === self::SIGNAL_STD_LINE){
			$client = strstr(substr($line, 1), " ", true);
			return substr($line, strlen($client) + 2);
		}
		return substr($line, 1);
	}
	public static function init(){
		self::$init = true;
		Command::$cmds = [
			"PASS" => PassCommand::class,
			"NICK" => NickCommand::class,
			"USER" => UserCommand::class,

			// TODO more
		];
	}
}
