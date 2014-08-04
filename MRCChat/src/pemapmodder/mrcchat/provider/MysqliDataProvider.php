<?php

namespace pemapmodder\mrcchat\provider;

use pemapmodder\mrcchat\Channel;
use pemapmodder\mrcchat\MRCChat;

class MysqliDataProvider implements DataProvider{
	/** @var MRCChat */
	private $main;
	/** @var \mysqli */
	private $db;
	/** @var \WeakRef[] */
	private $channels = [];
	public function __construct(MRCChat $main, array $args){
		$this->main = $main;
		$this->db = new \mysqli($args["host"], $args["username"], $args["password"], $args["database"]);
		if($this->db->connect_error){
			// TODO
		}
	}
	public function getMain(){
		return $this->main;
	}
	public function close(){
		$this->db->close();
	}
	public function offsetExists($name){
		$result = $this->db->query("SELECT modes FROM mrcchat_channels WHERE name = '{$this->db->escape_string($name)}';");
		$success = is_array($result->fetch_assoc());
		$result->close();
		return $success;
	}
	public function offsetGet($name){
		$this->collectGarbage();
		if(isset($this->channels[strtolower($name)])){
			return $this->channels[strtolower($name)]->get();
		}
		$result = $this->db->query("SELECT * FROM mrcchat_channels WHERE name = '{$this->db->escape_string($name)}';");
		$data = $result->fetch_assoc();
		$result->close();
		return new Channel($data["name"], $data["modes"]);
	}
	public function isAvailable(){
		return $this->db->ping();
	}
	private function collectGarbage(){
		foreach($this->channels as $name => $channel){
			if(!$channel->valid()){
				unset($this->channels[$name]);
			}
		}
	}
}
