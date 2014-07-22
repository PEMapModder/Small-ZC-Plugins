<?php

namespace pemapmodder\customareas;

use pocketmine\level\Level;
use pocketmine\level\Position;

class Area{
	const LIMIT_BUILD           = 0b0000000000000000;
	const LIMIT_PVP             = 0b0000000000000000;
	const LIMIT_FIGHT           = 0b0000000000000000;
	const LIMIT_OPEN_CHEST      = 0b0000000000000000;
	const LIMIT_FURNACE         = 0b0000000000000000;
	const AUTO_HEAL             = 0b0000000000000000;
	const AUTO_DAMAGE           = 0b0000000000000000;
	public static $nodes = ["build", "fight"];
	/** @var Position */
	protected $from, $to;
	private $flags;
	private $name;
	private $physical;
	public function __construct(PhysicalArea $area, Level $level, $flags, $name){
		$this->physical = $area;
		$this->flags = $flags;
		$this->name = $name;
	}
	/**
	 * @return string
	 */
	public function getName(){
		return $this->name;
	}
	/**
	 * @return int
	 */
	public function getFlags(){
		return $this->flags;
	}
	/**
	 * @param int $flags
	 */
	public function setFlags($flags){
		$this->flags = $flags;
	}
	public function hasFlags($flag, $isOR = false){
		$left = $flag & $this->flags;
		if($isOR){
			return $left > 0 or $flag === 0;
		}
		return $left === $flag;
	}
	public function setFlag($flag, $bool){
		if($bool){
			$this->flags |= $flag;
		}
		else{
			$this->flags &= ~$flag;
		}
	}
}
