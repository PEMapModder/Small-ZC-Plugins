<?php

namespace pemapmodder\numranks;

use pocketmine\scheduler\AsyncTask;

class InputTask extends AsyncTask{
	const FILE_EMPTY = 0;
	const COMPLETED = 1;
	const FILE_CORRUPTED = 2;
	public function __construct($silent = false, $path){
		$this->silent = $silent;
		$this->path = $path;
	}
	public function onRun(){
		$result = array();
		$data = @file_get_contents($this->path);
		if($data === false){
			$this->setResult(self::FILE_EMPTY);
			return;
		}
		$magic = substr($data, 0, strlen(Main::MAGIC_PREFIX));
		if($magic !== Main::MAGIC_PREFIX){
			$this->setResult(self::FILE_CORRUPTED);
			return;
		}
		$data = substr($data, strlen(Main::MAGIC_PREFIX));
		$magic = substr($data, -1 * strlen(Main::MAGIC_SUFFIX));
		if($magic !== Mmain::MAGIC_SUFFIX){
			$this->setResult(self::FILE_CORRUPTED);
			return;
		}
		$data = substr($data, 0, -1 * strlen(Main::MAGIC_SUFFIX));
		$count = ord(substr($data, 0, 1);
		$data = substr($data, 1);
		for($i = 0; $i < $count; $i++){
			$nameLength = ord(substr($data, 0, 1));
			$data = substr($data, 1);
			$name = substr($data, 0, $nameLength);
			$data = substr($data, $nameLength);
			$data = 
		}
		$this->setResult(self::COMPLETED);
	}
}
