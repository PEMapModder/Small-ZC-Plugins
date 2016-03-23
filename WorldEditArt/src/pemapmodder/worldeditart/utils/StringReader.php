<?php

namespace pemapmodder\worldeditart\utils;

use pocketmine\utils\Binary;

class StringReader{
	private $buffer;

	public static function fromFile($path){
		$buffer = @file_get_contents($path);
		if($buffer === false){
			return false;
		}
		return new StringReader($buffer);
	}

	public function __construct($buffer){
		$this->buffer = str_split($buffer);
	}

	public function read($length = 1){
		for($buffer = ""; $length > 0; $length--){
			if($this->feof()){
				throw new \Exception("Unexpected end of file");
			}
			$buffer .= array_shift($this->buffer);
		}
		return $buffer;
	}

	public function readLine(){
		$buffer = "";
		while(!$this->feof()){
			$buffer .= ($char = array_shift($this->buffer));
			if($char === "\n"){
				return $buffer;
			}
			if($char === "\r"){
				if($this->buffer[0] !== "\n"){
					return $buffer;
				}
			}
		}
		return $buffer;
	}

	public function readAllNext(){
		return implode("", $this->buffer);
	}

	public function readBString(){
		return $this->read($this->readByte(false));
	}

	public function readSString(){
		return $this->read($this->readShort(false));
	}

	public function readTString(){
		return $this->read($this->readTriad(false));
	}

	public function readIString(){
		return $this->read($this->readInt(false));
	}

	public function readLString(){
		return $this->read($this->readLong(false));
	}

	public function readByte($signed = true){
		return Binary::readByte($this->read(), $signed);
	}

	public function readShort($signed = true){
		return Binary::readShort($this->read(2), $signed);
	}

	public function readTriad($signed = true){
		$triad = Binary::readTriad($this->read(3));
		if(!$signed and $triad < 0){
			$triad += 0x1000000;
		}
		return $triad;
	}

	public function readInt($signed = true){
		$int = Binary::readInt($this->read(4));
		if(!$signed and $int < 0){
			$int += 0x100000000;
		}
		return $int;
	}

	public function readLong($signed = true){
		return Binary::readLong($this->read(8), $signed);
	}

	public function readFloat(){
		return Binary::readFloat($this->read(4));
	}

	public function readDouble(){
		return Binary::readDouble($this->read(8));
	}

	public function feof(){
		return count($this->buffer) > 0;
	}
}
