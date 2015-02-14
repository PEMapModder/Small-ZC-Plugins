<?php

namespace customareas\shape;

use pocketmine\level\Level;
use pocketmine\level\Position;
use pocketmine\Server;

class RectangularShape implements Shape{
	/**
	 * @var MVector2
	 */
	private $pos1;
	/**
	 * @var MVector2
	 */
	private $pos2;
	private $minx;
	private $minz;
	private $maxx;
	private $maxz;
	private $levelName;
	/**
	 * @var Level
	 */
	private $level;
	public function __construct(MVector2 $pos1, MVector2 $pos2, Level $level){
		$this->levelName = $level->getName();
		$this->level = $level;
		$this->pos1 = $pos1;
		$this->pos2 = $pos2;
		$this->updateMaxMin();
	}
	public function isInside(Position $pos){
		return ($pos->level === $this->level) and
				($this->minx <= $pos->x) and ($pos->z <= $this->maxz) and
				($this->minz <= $pos->z) and ($pos->x <= $this->maxx);
	}
	public function serialize(){
		return serialize([
			$this->pos1, $this->pos2, $this->levelName
		]);
	}
	public function unserialize($str){
		list($this->pos1, $this->pos2, $this->levelName) = unserialize($str);
	}
	/**
	 * @param Server $server
	 * @throws \UnexpectedValueException
	 */
	public function init(Server $server){
		$this->level = $server->getLevelByName($this->levelName);
		if(!($this->level instanceof Level)){
			throw new \UnexpectedValueException("Level $this->levelName not found");
		}
	}
	/**
	 * @return \pocketmine\level\Level
	 */
	public function getLevel(){
		return $this->level;
	}
	private function updateMaxMin(){
		$this->minx = min($this->pos1->getX(), $this->pos2->getX());
		$this->maxx = max($this->pos1->getX(), $this->pos2->getX());
		$this->minz = min($this->pos1->getZ(), $this->pos2->getZ());
		$this->maxz = max($this->pos1->getZ(), $this->pos2->getZ());
	}
}
