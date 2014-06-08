<?php

namespace pemapmodder\worldeditart\utils;

use pocketmine\event\block\BlockEvent;
use pocketmine\level\Position;
use pocketmine\math\Vector3;
use pocketmine\Player;

class Macro{
	/** @var Player */
	private $player;
	/** @var Position */
	private $ref;
	/** @var Vector3[] $log */
	private $log = [];
	/** @var \pocketmine\block\Block */
	private $blockLog = [];
	public function __construct(Player $player, Position $referencePoint){
		$this->ref = $referencePoint;
		$this->player = $player;
	}
	public function getPlayer(){
		return $this->player;
	}
	public function getRefPt(){
		return $this->ref;
	}
	public function addLog(BlockEvent $event){
		$b = $event->getBlock();
		$this->log[] = new Vector3($b->getX() - $this->ref->getX(), $b->getY() - $this->ref->getY(), $b->getZ() - $this->ref->getZ());
		$this->blockLog[] = $b;
	}
	public function run(Position $ref, $count = -1){
		if($count === -1){
			$count = count($this->log);
		}
		for($i = 0; $i < $count; $i++){
			$this->runLog($i, $ref);
		}
	}
	public function runLog($index, Position $ref){
		$ref->getLevel()->setBlock($ref->add($this->log[$index]), $this->blockLog[$index], false, false, true);
	}
}
