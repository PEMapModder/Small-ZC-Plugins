<?php

namespace pemapmodder\worldeditart\utils\provider\player;

class YAMLFilePlayerDataProvider extends FilePlayerDataProvider{
	public function getName(){
		return "JSON Player Data Provider";
	}

	public function emitFile($file, $data){
		yaml_emit_file($file, $data, YAML_UTF8_ENCODING);
	}

	public function parseFile($file){
		return yaml_parse_file($file);
	}
}
