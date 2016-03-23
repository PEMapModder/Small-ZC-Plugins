<?php

namespace pemapmodder\worldeditart\utils;

use pocketmine\utils\Binary;

class StringWriter{
	private $buffer = "";

	public function append($str){
		$this->buffer .= $str;
	}

	public function appendBString($str){
		$this->buffer .= chr(strlen($str));
		$this->buffer .= $str;
	}

	public function appendSString($str){
		$this->appendShort(strlen($str));
		$this->buffer .= $str;
	}

	public function appendTString($str){
		$this->appendTriad(strlen($str));
		$this->buffer .= $str;
	}

	public function appendIString($str){
		$this->appendInt(strlen($str));
		$this->buffer .= $str;
	}

	public function appendLString($str){
		$this->appendLong(strlen($str));
		$this->buffer .= $str;
	}

	public function appendByte($v){
		$this->buffer .= chr($v);
	}

	public function appendShort($v){
		$this->buffer .= Binary::writeShort($v);
	}

	public function appendTriad($v){
		$this->buffer .= Binary::writeTriad($v);
	}

	public function appendInt($v){
		$this->buffer .= Binary::writeInt($v);
	}

	public function appendLong($v){
		$this->buffer .= Binary::writeLong($v);
	}

	public function appendFloat($v){
		$this->buffer .= Binary::writeFloat($v);
	}

	public function appendDouble($v){
		$this->buffer .= Binary::writeDouble($v);
	}

	public function save($file, $mode = "wb"){
		@mkdir(dirname($file), 0777, true);
		$res = fopen($file, $mode);
		if(!is_resource($res)){
			return false;
		}
		fwrite($res, $this->buffer);
		fclose($res);
		$this->clear();
		return true;
	}

	public function clear(){
		$this->buffer = "";
	}
}
