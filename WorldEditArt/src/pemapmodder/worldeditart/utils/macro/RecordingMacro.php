<?php

namespace pemapmodder\worldeditart\utils\macro;

use pocketmine\block\Air;
use pocketmine\block\Block;
use pocketmine\math\Vector3;
use pocketmine\Player;
use pocketmine\utils\Binary;

class RecordingMacro{
	private $author;
	/** @var Vector3 */
	private $anchor;
	/** @var MacroOperation[] */
	private $log = [];
	private $hibernating;
	/**
	 * @param string|Player $author
	 * @param Vector3 $anchor
	 */
	public function __construct($author, Vector3 $anchor){
		if($author instanceof Player){
			$author = $author->getName();
		}
		$this->author = $author;
		$this->anchor = clone $anchor;
	}
	public function setHibernating($value){
		$this->hibernating = (boolean) $value;
	}
	public function addLog(Vector3 $pos, Block $block, $isBreak){
		if($this->hibernating){
			return;
		}
		$this->log[] = new MacroOperation($pos->subtract($this->anchor->x, $this->anchor->y, $this->anchor->z), $isBreak ? new Air:$block);
	}
	public function __toString(){
		$output = Binary::writeLong(count($this->log));
		foreach($this->log as $op){
			$output .= "$op";
		}
		return $output;
	}
	public function saveTo($res, $close = true){
		fwrite($res, "$this");
		if($close){
			fclose($res);
		}
	}
}
