<?php

namespace chatchannels;

use pocketmine\utils\Config;

class Configuration{
	/** @var array */
	private $config;
	public function __construct(Config $config){
		$this->config = $config->getAll();
	}
	public function getConsoleName(){
		return $this->config["console name"];
	}
	public function getDefaultChannel(){
		return $this->config["default channel name"];
	}
}
