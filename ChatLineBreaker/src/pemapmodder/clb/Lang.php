<?php

namespace pemapmodder\clb;

class Lang implements \ArrayAccess{
	public $data = array();
	public function __construct($path){
		$this->path = $path;
		$this->default = [
			"calibrate.instruction.close.screen" => "First, please close your chat screen.",
			"calibrate.instruction.next.message" => "Now look at the above message:",
			"calibrate.instruction.hyphens.separator" => "-------------------------",
			"calibrate.instruction.ask.number" => "What is the last number visible? It is your CLB length.",
			"calibrate.instruction.require.type.chat" => "Type your CLB length in chat directly.",
			"calibrate.response.not.numeric" => "Please type in the length!",
			"calibrate.response.too.low" => "I don't believe you! I don't think your device can only show @char characters!",
			"calibrate.response.too.high" => "Sorry, our database does not support numbers larger than 127. I don't believe you have such a mega machine though to show @char characters.",
			"calibrate.response.succeed" => "Your CLB length is now @char.",
			"view.on" => "CLB is enabled for you",
			"view.off" => "CLB is disabled for you",
			"view.length" => "Your CLB length is @length",
			"toggle.on" => "CLB is now enabled for you",
			"toggle.off" => "CLB is now disabled for you",
			"cmd.console.reject" => "Right-click the console or edit start.cmd to change your fonts, not here.",

		];
		if(is_file($this->path)){
			$this->data = $this->default;
			$this->load();
		}
		else{
			$this->data = $this->default;
			$this->save();
		}
	}
	public function offsetGet($k){
		if(isset($this->data[$k])){
			return $this->data[$k];
		}
		return $this->data[$k];
	}
	public function offsetSet($k, $v){
		$this->data[$k] = $v;
	}
	public function offsetUnset($k){
		unset($this->data[$k]);
	}
	public function offsetExists($k){
		return isset($this->data[$k]) or isset($this->default[$k]);
	}
	public function save(){
		$output = "";
		foreach($this->data as $key=>$value){
			$k = strpos($key, "=") === false ? $key : (strpos($key, "'=") === false ? "'$key'" : "\"$key\"");
			$output .= "$k=$value";
			$output .= PHP_EOL;
		}
		file_put_contents($this->path, $output);
	}
	public function load(){
		foreach(explode(PHP_EOL, file_get_contents($this->path)) as $key=>$line){
			if(substr($line, 0, 1) === "#" or $line === ""){
				continue;
			}
			if(strpos($line, "=") === false){
				$this->error($key);
				continue;
			}
			if(strpos($line, "\"") === 0){
				$length = strpos($line, "\"", 1);
				if(substr($line, $length + 1, 1) !== "="){
					$this->error($key);
					continue;
				}
				$this->data[strstr(substr($line, 1), "\"=", true)] = substr($line, $length + 2);
				continue;
			}
			if(strpos($line, "'") === 0){
				$length = strpos($line, "'", 1);
				if(substr($line, $length + 1, 1) !== "="){
					$this->error($key);
					continue;
				}
				$this->data[strstr(substr($line, 1), "'=", true)] = substr($line, $length + 2);
				continue;
			}
			$this->data[strstr($line, "=", true)] = substr(strstr($line, "="), 1);
		}
	}
	protected function error($line){
		trigger_error("Syntax error on line $line at {$this->path} lang file", E_USER_WARNING);
	}
}
