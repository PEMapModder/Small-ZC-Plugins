<?php

namespace pemapmodder\nailedkeyboard;

class Line{
	/** @var string */
	private $text;
	/** @var int */
	private $pointer, /** @noinspection PhpUnusedPrivateFieldInspection */ $selectFrom = null;
	/** @var string */
	private $clipboard = null;
	public function __construct($text = "", $pointer = 0){
		$this->text = $text;
		$this->pointer = $pointer;
		$this->selectFrom = null;
	}
	public function startSelection(){
		$this->selectFrom = $this->pointer;
	}
	public function deselect(){
		$this->selectFrom = null;
	}
	public function copy(){
		if($this->selectFrom = null){
			throw new \UnexpectedValueException;
		}
		$this->clipboard = self::substr($this->text, min($this->pointer, $this->selectFrom), max($this->pointer, $this->pointer));
	}
	public function cut(){
		if($this->selectFrom === null){
			throw new \UnexpectedValueException;
		}
		$from = min($this->pointer, $this->selectFrom);
		$to = max($this->pointer, $this->selectFrom);
		$this->clipboard = self::substr($this->text, $from, $to);
		$this->text = self::substr($this->text, 0, $from) . self::substr($this->text, $to);
		$this->selectFrom = null;
		$this->pointer = $from;
	}
	public function paste(){
		if($this->clipboard === null){
			throw new \UnexpectedValueException;
		}
		$this->input($this->clipboard);
	}
	public function clearClipboard(){
		$copy = $this->clipboard;
		$this->clipboard = null;
		return $copy;
	}
	public function getSelectedText(){
		return $this->selectFrom === null ? null:self::substr($this->text, min($this->pointer, $this->selectFrom), max($this->pointer, $this->selectFrom));
	}
	public function getClipboard(){
		return $this->clipboard;
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
		if($this->selectFrom !== null){
			$this->text = self::substr($this->text, 0, min($this->pointer, $this->selectFrom)) . self::substr($this->text, max($this->pointer, $this->selectFrom));
			$this->pointer = min($this->pointer, $this->selectFrom);
			$this->selectFrom = null;
			return;
		}
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
		if($this->selectFrom !== null){
			$this->text = self::substr($this->text, 0, min($this->pointer, $this->selectFrom)) . self::substr($this->text, max($this->pointer, $this->selectFrom));
			$this->pointer = min($this->pointer, $this->selectFrom);
			$this->selectFrom = null;
			return;
		}
		if($this->pointer === self::strlen($this->text)){
			throw new \OutOfBoundsException;
		}
		$left = $this->getLeftText();
		$right = self::substr($this->getRightText(), 1);
		$this->text = $left . $right;
	}
	public function input($input){
		if($this->selectFrom !== null){
			$this->text = self::substr($this->text, 0, min($this->pointer, $this->selectFrom)) . self::substr($this->text, max($this->pointer, $this->selectFrom));
			$this->pointer = min($this->pointer, $this->selectFrom);
			$this->selectFrom = null;
		}
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
