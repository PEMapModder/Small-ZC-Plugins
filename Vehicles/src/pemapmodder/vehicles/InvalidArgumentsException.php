<?php

namespace pemapmodder\vehicles;

class InvalidArgumentsException extends \Exception{
	/**
	 * @var string[]
	 */
	protected $args;
	/**
	 * @var string
	 */
	protected $correctUsage;
	/**
	 * @param string[] $args
	 * @param string $correct
	 */
	public function __construct(array $args, $correct){
		$this->args = $args;
		$this->correctUsage = $correct;
	}
	public function __toString(){
		return "Wrong usage of vehicles command.";
	}
	public function getCorrectUsage(){
		return $this->correctUsage;
	}
	public function getArgs(){
		return $this->args;
	}
}
