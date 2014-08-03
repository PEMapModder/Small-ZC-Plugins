<?php

namespace pemapmodder\worldeditart\utils\macro;

use pocketmine\block\Air;
use pocketmine\block\Block;
use pocketmine\math\Vector3;
use pocketmine\nbt\NBT;
use pocketmine\nbt\tag;
use pocketmine\Player;

class RecordingMacro{
	private $author;
	private $description = "";
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
	public function isHibernating(){
		return $this->hibernating;
	}
	public function addLog(Vector3 $pos, Block $block, $isBreak){
		if($this->hibernating){
			return;
		}
		$this->log[] = new MacroOperation($pos->subtract($this->anchor->x, $this->anchor->y, $this->anchor->z), $isBreak ? new Air:$block);
	}
	public function addWait($ticks){
		if($this->isHibernating()){
			return;
		}
		$this->log[] = new MacroOperation($ticks);
	}
	public function saveTo($file, $compression = 31){
		$tag = new tag\Compound;
		$tag["author"] = new tag\String("author", $this->author);
		$tag["description"] = new tag\String("description", $this->description);
		$tag["ops"] = new tag\Enum("ops");
		foreach($this->log as $i => $log){
			$tag["ops"][$i] = $log->toTag();
		}
		$nbt = new NBT;
		$nbt->setData($tag);
		$stream = @fopen($file, "wb");
		if(!is_resource($stream)){
			throw new \RuntimeException("Unable to open stream. Maybe the macro name is not a valid filename?");
		}
		if($compression !== false){
			$data = chr($compression).$nbt->writeCompressed($compression);
		}
		else{
			$data = chr(0).$nbt->write();
		}
		$cnt = fwrite($stream, $data);
		if($cnt !== strlen($data)){
			throw new \RuntimeException("Cannot write contents to the file.");
		}
		fclose($stream);
	}
}
