<?php

namespace pemapmodder\nailedkeyboard;

class Line{
	/** @var string */
	private $text;
	/** @var int */
	private $pointer, /** @noinspection PhpUnusedPrivateFieldInspection */ $selectFrom = null;
	public function __construct($text = "", $pointer = 0){
		$this->text = $text;
	}
	public function left(){
		if($this->pointer === 0){
			throw new \OutOfBoundsException;
		}
		$this->pointer--;
	}
	public function right(){
		if($this->pointer === self::strlen($this->text)){
			throw new \OutOfBoundsException;
		}
		$this->pointer++;
	}
	public function backspace(){
		if($this->pointer === 0){
			throw new \OutOfBoundsException;
		}
		$left = $this->getLeftText();
		$right = $this->getRightText();
		$left = self::substr($left, 0, self::strlen($left) - 1);
		$this->text = $left . $right;
		$this->pointer--;
	}
	public function delete(){
		if($this->pointer === self::strlen($this->text)){
			throw new \OutOfBoundsException;
		}
		$left = $this->getLeftText();
		$right = self::substr($this->getRightText(), 1);
		$this->text = $left . $right;
	}
	public function input($input){
		$left = self::substr($this->text, 0, $this->pointer);
		$right = self::substr($this->text, $this->pointer);
		$this->text = $left . $input . $right;
		$this->pointer += self::strlen($input);
	}
	public function home(){
		$this->pointer = 0;
	}
	public function end(){
		$this->pointer = self::strlen($this->text);
	}
	public function getLeftText(){
		return self::substr($this->text, 0, $this->pointer);
	}
	public function getRightText(){
		return self::substr($this->text, $this->pointer);
	}
	public function getText(){
		return $this->text;
	}
	public function reset(){
		$this->pointer = 0;
		$this->text = "";
	}
	public static function strlen($string){
		if(function_exists("mb_strlen")){
			return mb_strlen($string);
		}
		else{
			return strlen($string); // :(
		}
	}
	public static function substr($text, $from, $length = null){
		if(function_exists("mb_substr")){
			return mb_substr($text, $from, $length);
		}
		else{
			return substr($text, $from, $length); // :(
		}
	}
}
