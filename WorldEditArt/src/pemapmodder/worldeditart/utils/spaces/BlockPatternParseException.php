<?php

namespace pemapmodder\worldeditart\utils\spaces;

class BlockPatternParseException extends \Exception{
	/** @var string */
	private $pattern;

	/**
	 * @param string $pattern
	 * @param string $message
	 */
	public function __construct($pattern, $message){
		parent::__construct($message);
		$this->pattern = $pattern;
	}

	/**
	 * @return string
	 */
	public function getPattern(){
		return $this->pattern;
	}
}
