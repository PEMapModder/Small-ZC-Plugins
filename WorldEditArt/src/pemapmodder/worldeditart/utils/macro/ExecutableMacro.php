<?php

namespace pemapmodder\worldeditart\utils\macro;

use pocketmine\level\Position;

class ExecutableMacro{
	private $author;
	/** @var MacroOperation[] */
	private $ops = [];
	public function __construct($string){
		$offset = 0;
		$length = substr($string, 0, 1); $offset += 1;
		$this->author = substr($string, $offset, $length); $offset += 8;
		$length = substr($string, $offset, 8); $offset += 8;
		$data = substr($string, $offset);
		if(strlen($data) !== $length * 20){
			trigger_error("Macro file corrupted", E_USER_WARNING);
		}
		foreach(str_split($data, 20) as $op){
			$this->ops[] = MacroOperation::parse($op);
		}
	}
	/**
	 * @return mixed
	 */
	public function getAuthor(){
		return $this->author;
	}
	public function run(Position $pos){
		$cnt = 0;
		foreach($this->ops as $op){
			if($op->operate($pos) !== false){
				$cnt++;
			}
		}
	}
}
