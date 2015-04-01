<?php

namespace authtools;

use pocketmine\utils\Utils;

class Settings{
	public $maxFailures;
	public $logFile;
	public $logStream;
	public $destructed = false;
	public function __construct(AuthTools $main){
		$c = $main->getConfig();
		$this->maxFailures = $c->getNested("directauth.max-failures", 5);
		$logFile = $c->getNested("directauth.log-file", "/dev/null");
		if($logFile === "/dev/null"){
			$this->logFile = false;
		}elseif(substr($logFile, 0, 2) === "//"){
			$this->logFile = realpath("/") . ltrim($logFile, "/");
		}elseif(Utils::getOS() === "win" and preg_match('#^[A-Z]:[/\\].*$#i', $logFile)){
			$this->logFile = $logFile;
		}else{
			$this->logFile = $main->getDataFolder() . $logFile;
		}
		$this->logStream = fopen($this->logFile, "at");
	}
	public function log($msg){
		fwrite($this->logStream, $msg);
		fwrite($this->logStream, PHP_EOL);
	}
	public function __destruct(){
		if(!$this->destructed){
			fclose($this->logStream);
			$this->destructed = true;
		}
	}
}
