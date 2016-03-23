<?php

namespace pemapmodder\ircbridge\bridge\protocol;

abstract class Command{
	public static $cmds = [];
	/** @var string */
	private $name;

	public function __construct(IRCLine $line){
		$this->name = $line->getCmdName();
		$this->init($line->getArguments(), $line->getPrefix());
	}

	/**
	 * @return string
	 */
	public function getName(){
		return $this->name;
	}

	/**
	 * @param string[] $args
	 * @param string   $prefix
	 */
	protected abstract function init($args, $prefix);

	public function encode(){
		throw new \RuntimeException(static::class . " cannot be encoded because it isn't sent from a server.");
	}
}
