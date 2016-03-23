<?php

namespace pemapmodder\clb;

use pocketmine\scheduler\AsyncTask;
use pocketmine\Server;

class WriteFileTask extends AsyncTask{
	/** @var string */
	private $file, $content;

	public function __construct($file, $content){
		$this->file = $file;
		$this->content = $content;
	}

	public function onRun(){
		file_put_contents($this->file, $this->content, \LOCK_EX);
	}

	public function onCompletion(Server $server){
	}
}
