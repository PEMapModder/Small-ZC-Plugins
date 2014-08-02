<?php

namespace pemapmodder\worldeditart\utils\macro;

use pemapmodder\worldeditart\Main;
use pocketmine\level\Position;
use pocketmine\nbt\NBT;
use pocketmine\nbt\tag;
use pocketmine\Server;

class ExecutableMacro{
	private $author;
	/** @var MacroOperation[] */
	private $ops = [];
	public function __construct($string){
		$nbt = new NBT;
		$nbt->readCompressed($string);
		$tag = $nbt->getData();
		/** @var tag\String $author */
		$author = $tag["author"];
		$this->author = $author->getValue();
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
	 * @return mixed
	 */
	public function getAuthor(){
		return $this->author;
	}
}
