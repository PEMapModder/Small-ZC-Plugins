<?php

namespace pemapmodder\worldeditart\utils\provider\player;

class JSONFilePlayerDataProvider extends FilePlayerDataProvider{
	public function getName(){
		return "JSON Player Data Provider";
	}
	public function emitFile($file, $data){
		file_put_contents($file, $data, json_encode($file,
			$this->getMain()->getConfig()->get("data providers")["player"]["json"]["pretty print"] ?
				JSON_PRETTY_PRINT | JSON_BIGINT_AS_STRING : JSON_BIGINT_AS_STRING));
	}
	public function parseFile($file){
		return json_decode(file_get_contents($file));
	}
}
