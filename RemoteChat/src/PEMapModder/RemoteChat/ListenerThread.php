<?php

namespace PEMapModder\RemoteChat;

use LogLevel;
use pocketmine\Thread;

class ListenerThread extends Thread{
	const CLASS_LOG = 0;
	const CLASS_REQUEST = 1;
	public static $SUPPORTED_VERSIONS = [1 => true];
	/** @var int */
	private $port;
	/** @var string[] */
	private $blacklist, $whitelist;
	/** @var bool */
	private $whitelistOn;
	/** @var resource */
	private $sock;
	/** @var string[] */
	private $hostNameCache = [];
	public $lock = false;
	public $buffer = "a:0:{}";
	public $stopCmd = false;
	public $terminated = false;
	public $terminateReason = "";

	public function __construct($port, $blacklist, $whitelist, $whitelistOn){
		$this->port = $port;
		$this->blacklist = $blacklist;
		$this->whitelist = $whitelist;
		$this->whitelistOn = $whitelistOn;
	}

	public function pushDatum($datum){
		$this->synchronized(function ($datum){
			$data = unserialize($this->buffer);
			$data[] = $datum;
			$this->buffer = serialize($data);
		}, $datum);
	}

	private function pushLog($msg, $logLevel = LogLevel::INFO){
		$this->pushDatum([
			"c" => self::CLASS_LOG,
			"lm" => $msg,
			"ll" => $logLevel,
		]);
	}

	private function pushRequest($action, $params, $ip, $port){
		$this->pushDatum([
			"c" => self::CLASS_REQUEST,
			"ra" => $action,
			"rp" => $params,
			"_ip" => $ip,
			"_port" => $port,
		]);
	}

	public function pullData(){
		return $this->synchronized(function (){
			$r = unserialize($this->buffer);
			$this->buffer = "a:0:{}";
			return $r;
		});
	}

	public function acquire(){
		while($this->lock){
			;
		}
		$this->lock = true;
	}

	public function release(){
		$this->lock = false;
	}

	public function run(){
		if($this->whitelistOn){
			foreach($this->whitelist as &$white){
				$white = $this->resolveAddress($white);
			}
		}
		foreach($this->blacklist as &$black){
			$black = $this->resolveAddress($black);
		}
		$this->sock = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
		if(!is_resource($this->sock)){
			$this->terminate("Could not create socket - " . socket_strerror(socket_last_error()));
			return;
		}
		if(socket_bind($this->sock, "0.0.0.0", $this->port) === false){
			$this->terminate("Could not bind socket to 0.0.0.0:$this->port - " . socket_strerror(socket_last_error($this->sock)));
			return;
		}
		if(socket_listen($this->sock, 10) === false){
			$this->terminate("Could not listen to socket - " . socket_strerror(socket_last_error($this->sock)));
			return;
		}
		socket_set_block($this->sock);
		$this->onRun();
	}

	private function terminate($msg){
		$this->terminated = true;
		$this->terminateReason = $msg;
	}

	private function onRun(){
		while(!$this->stopCmd){
			$request = socket_accept($this->sock);
			if(!is_resource($request)){
				continue;
			}
			$this->processRequest($request);
		}
	}

	private function processRequest($req){
		socket_getpeername($req, $ip, $port);
		if(!$this->allowConnect($ip)){
			socket_close($req); // don't even log it
			return;
		}
		$line1 = socket_read($req, 4096, PHP_NORMAL_READ);
		if(!is_string($line1)){
			socket_close($req);
			return;
		}
		$cmd = explode(" ", trim($line1));
		if(!isset($cmd[3])){
			$this->pushLog("Connection from $ip:$port sent an incomplete header line.", LogLevel::WARNING);
			socket_close($req);
			return;
		}
		list($identifier, $version, $action, $hostname) = $cmd;
		if($identifier !== "REMOTECHAT"){
			$this->pushLog("Connection from $ip:$port attempted to send a non-RemoteChat request.", LogLevel::WARNING);
			socket_close($req);
			return;
		}
		$version = (int) $version;
		if(!isset(self::$SUPPORTED_VERSIONS[$version])){
			$this->pushLog("Connection from $ip:$port is using an unsupported protocol version #$version.", LogLevel::WARNING);
			socket_close($req);
			return;
		}
		if($hostname !== "" and $hostname !== "null"){
			$resolved = $this->resolveAddress($hostname);
			if($resolved !== $ip){
				$this->pushLog("Connection from $ip:$port claimed to be from $hostname ($resolved)!", LogLevel::WARNING);
				socket_close($req);
				return;
			}
		}else{
			$hostname = $ip;
		}
		// execute!
		switch($action){
			case "PRIVMSG":
				$line2 = socket_read($req, 255, PHP_NORMAL_READ);
				if(!is_string($line2)){
					$this->pushLog("PRIVMSG request from $ip:$port ($hostname) provided insufficient parameters.", LogLevel::WARNING);
					socket_close($req);
					return;
				}
				$replyTo = trim($line2);
				$line3 = socket_read($req, 255, PHP_NORMAL_READ);
				if(!is_string($line3)){
					$this->pushLog("PRIVMSG request from $ip:$port ($hostname) provided insufficient parameters.", LogLevel::WARNING);
					socket_close($req);
					return;
				}
				$target = trim($line3);
				$line4 = socket_read($req, 16383, PHP_NORMAL_READ);
				if(!is_string($line4)){
					$this->pushLog("PRIVMSG request from $ip:$port ($hostname) provided insufficient parameters.", LogLevel::WARNING);
					socket_close($req);
					return;
				}
				$message = trim($line4);
				$this->pushRequest("PRIVMSG", [$replyTo, $target, $message], $ip, $port);
				socket_close($req);
				return;
		}
	}

	private function resolveAddress($address, $force = false){
		if(!$force and isset($this->hostNameCache[$address])){
			return $this->hostNameCache[$address];
		}
		$ip = gethostbyname($address);
		return $this->hostNameCache[$address] = $ip;
	}

	private function allowConnect($ip){
		return !in_array($ip, $this->blacklist) and (!$this->whitelistOn or in_array($ip, $this->whitelist));
	}
}
