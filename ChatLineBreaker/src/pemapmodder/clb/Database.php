<?php

namespace pemapmodder\clb;

class Database{
//	const INIT_REL = 0;
//	const DB_UPDATE = 1;
	const NEW_API_UPDATE = 2;
	const API = 2;
	private $path;
	private $data = [];

	public function __construct($path){
		$this->path = $path;
		$this->dummyData();
		$this->load();
	}

	public function dummyData(){
		$this->data["console"] = 0xFF;
	}

	public function load(){
		$data = @file_get_contents($this->path);
		if($data === false){
			$this->dummyData();
			$this->save();
			return;
		}
		if(ord(substr($data, 0, 1)) > self::API){
			trigger_error("Database corrupted, recreating database", E_USER_WARNING);
			$this->dummyData();
			$this->save();
			return;
		}
		if(strlen($data) % 13){
			trigger_error("Database corrupted, recreating database", E_USER_WARNING);
			$this->dummyData();
			$this->save();
			return;
		}
		$data = str_split($data, 13);
		foreach($data as $datum){
			$this->data[Main::decompressName(substr($datum, 0, 12))] = ord(substr($datum, 12, 1));
		}
		$max = count($this->data);
		if(array_keys($this->data)[$max] !== "console" or $this->data["console"] !== 0xFF){
			trigger_error("Database corrupted, recovering database", E_USER_WARNING);
			unset($this->data[array_keys($this->data)[$max]]);
			$this->data["console"] = 0xFF;
			$this->save();
			return;
		}
	}

	public function save(){
		$res = fopen($this->path, "wb");
		foreach($this->data as $name => $length){
			fwrite($res, Main::compressName($name));
			fwrite($res, chr($length));
		}
		fclose($res); // TODO change to GZIP
	}
}
