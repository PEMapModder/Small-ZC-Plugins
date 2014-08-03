<?php

namespace pemapmodder\worldeditart\utils\macro;

use pemapmodder\worldeditart\Main;
use pocketmine\level\Position;
use pocketmine\nbt\NBT;
use pocketmine\nbt\tag;
use pocketmine\Server;

class ExecutableMacro{
	/** @var string */
	private $author, $description;
	/** @var MacroOperation[] */
	private $ops = [];
	public function __construct($string){
		$nbt = new NBT;
		$type = ord(substr($string, 0, 1));
		$string = substr($string, 1);
		if($type === 0){
			$nbt->read($string);
		}
		else{
			$nbt->readCompressed($string, $type);
		}
		$tag = $nbt->getData();
		$this->author = $tag["author"];
		$this->description = $tag["description"];
		/** @var tag\Enum $ops */
		$ops = $tag["ops"];
		/** @var tag\Compound $op */
		foreach($ops as $op){
			$this->ops[] = MacroOperation::fromTag($op);
		}
	}
	public function execute(Server $server, Position $anchor, Main $main){
		$ticks = 0;
		foreach($this->ops as $op){
			if($op->getType() === MacroOperation::TYPE_WAIT){
				$ticks += $op->getLength();
				continue;
			}
			$anchor->level->acquire(); // StrongRef
			$server->getScheduler()->scheduleDelayedTask(new MacroOperationTask($main, $op, $anchor), $ticks);
		}
	}
	/**
	 * @return string
	 */
	public function getAuthor(){
		return $this->author;
	}
	/**
	 * @return string
	 */
	public function getDescription(){
		return $this->description;
	}
}
