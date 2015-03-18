<?php

namespace pemapmodder\ircbridge\bridge;

class Buffer{
	private $write = "";
	private $read = "";
	private $lock = false;
	public function nextWrite(){
		while($this->lock);
		$this->lock = true;
		$result = strstr($this->write, "\r\n", true);
		$this->write = substr($this->write, strlen($result) + 2);
		$this->lock = false;
		return $result;
	}
	public function nextRead(){
		while($this->lock);
		$this->lock = true;
		$result = strstr($this->read, "\r\n", true);
		$this->read = substr($this->read, strlen($result) + 2);
		$this->lock = false;
		return $result;
	}
	public function addWrite($line){
		while($this->lock);
		$this->lock = true;
		$this->write .= $line . "\r\n";
		$this->lock = false;
	}
	public function addRead($line){
		while($this->lock);
		$this->lock = true;
		$this->read .= $line . "\r\n";
		$this->lock = false;
	}
	public function hasMoreWrite(){
		return $this->write !== "";
	}
	public function hasMoreRead(){
		return $this->read !== "";
	}
}
