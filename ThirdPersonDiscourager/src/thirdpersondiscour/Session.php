<?php

namespace thirdpersondiscour;

use pocketmine\block\Block;
use pocketmine\network\protocol\UpdateBlockPacket;
use pocketmine\Player;

class Session{
	const X = 1;
	const Y = 0;
	const Z = 2;
	private static $conversionTable = [
		0 => 1,
		1 => 2,
		2 => 1,
		3 => 2
	];
	/** @var ThirdPersonDiscour */
	private $main;
	/** @var Player */
	private $player;
	private $enabled = false;
	/** @var Block[] coordinate-sensitive block array */
	private $overridenBlocks = [];
	private $block;
	private $meta;
	public function __construct(ThirdPersonDiscour $main, Player $player){
		$this->main = $main;
		$this->player = $player;
		$this->block = $this->main->getBlockType()->getId();
		$this->meta = $this->main->getBlockType()->getDamage();
	}
	public function update(){
		if(!$this->enabled){
			return;
		}
		$this->resetOverriden();
		$this->spawnDiscourager();
	}
	public function resetOverriden(){
		foreach($this->overridenBlocks as $b){
			$pk = new UpdateBlockPacket;
			$pk->x = $b->x;
			$pk->y = $b->y;
			$pk->z = $b->z;
			$pk->block = $b->getId();
			$pk->meta = $b->getDamage();
			$this->player->dataPacket($pk);
		}
		$this->overridenBlocks = [];
	}
	public function spawnDiscourager(){
		$dir = $this->player->getDirectionVector();
		if($this->player->pitch > 45 or $this->player->pitch < -45){
			$face = 0; // y
		}else{
			$face = self::$conversionTable[$this->player->getDirection()];
		}
		$center = $this->player->subtract($dir->multiply($this->main->getDistance()))->add(0, $this->player->eyeHeight);
		$l = $this->player->getLevel();
		if($face === self::Y){
			$this->overridenBlocks = [
				$l->getBlock($center->add(1, 0, 1)),
				$l->getBlock($center->add(1, 0, 0)),
				$l->getBlock($center->add(1, 0, -1)),
				$l->getBlock($center->add(0, 0, 1)),
				$l->getBlock($center),
				$l->getBlock($center->add(0, 0, -1)),
				$l->getBlock($center->add(-1, 0, 1)),
				$l->getBlock($center->add(-1, 0, 0)),
				$l->getBlock($center->add(-1, 0, -1)),
			];
		}elseif($face === self::X){
			$this->overridenBlocks = [
				$l->getBlock($center->add(0, 1, 1)),
				$l->getBlock($center->add(0, 1, 0)),
				$l->getBlock($center->add(0, 1, -1)),
				$l->getBlock($center->add(0, 0, 1)),
				$l->getBlock($center),
				$l->getBlock($center->add(0, 0, -1)),
				$l->getBlock($center->add(0, -1, 1)),
				$l->getBlock($center->add(0, -1, 0)),
				$l->getBlock($center->add(0, -1, -1)),
			];
		}elseif($face === self::Z){
			$this->overridenBlocks = [
				$l->getBlock($center->add(1, 1, 0)),
				$l->getBlock($center->add(1, 0, 0)),
				$l->getBlock($center->add(1, -1, 0)),
				$l->getBlock($center->add(0, 1, 0)),
				$l->getBlock($center),
				$l->getBlock($center->add(0, -1, 0)),
				$l->getBlock($center->add(-1, 1, 0)),
				$l->getBlock($center->add(-1, 0, 0)),
				$l->getBlock($center->add(-1, -1, 0)),
			];
		}
		foreach($this->overridenBlocks as $b){
			$pk = new UpdateBlockPacket;
			$pk->x = $b->x;
			$pk->y = $b->y;
			$pk->z = $b->z;
			$pk->block = $this->main->getBlockType()->getId();
			$pk->meta = $this->main->getBlockType()->getDamage();
			$this->player->dataPacket($pk);
		}
	}
	/**
	 * @return boolean
	 */
	public function isEnabled(){
		return $this->enabled;
	}
	public function enable(){
		if($this->enabled){
			return;
		}
		$this->enabled = true;
		$this->spawnDiscourager();
	}
	public function disable(){
		if(!$this->enabled){
			return;
		}
		$this->enabled = false;
		$this->resetOverriden();
	}
	public function __toString(){
		return $this->player->getName();
	}
}
