<?php

namespace pemapmodder\worldeditart\utils\clip;

use pemapmodder\worldeditart\utils\provider\Cache;
use pemapmodder\worldeditart\utils\spaces\Space;
use pocketmine\block\Block;
use pocketmine\level\Position;
use pocketmine\math\Vector3;

class Clip implements Cache{
	const KEY_SEPARATOR = ":";
	private $name;
	private $blocks = [];
	private $creationTime;
	/**
	 * @param Block[]|Space $blocks
	 * @param Position|null $anchor
	 * @param string $name
	 */
	public function __construct($blocks = [], Position $anchor = null, $name = "default"){
		$this->name = $name;
		$this->creationTime = microtime(true);
		if($blocks instanceof Space){
			$anchor = $anchor->round();
			foreach($blocks->getBlockList() as $b){
				$this->add($b->subtract($anchor), $b);
			}
		}
		else{
			$this->blocks = $blocks;
		}
	}
	public function add(Vector3 $vectors, Block $block){
		$this->blocks[self::key($vectors)] = $block;
	}
	public static function key(Vector3 $v){
		$v = $v->floor();
		return $v->x.self::KEY_SEPARATOR.$v->y.self::KEY_SEPARATOR.$v->z;
	}
	public static function unkey($string){
		$tokens = explode(self::KEY_SEPARATOR, $string);
		return new Vector3((int) $tokens[0], (int) $tokens[1], (int) $tokens[2]);
	}
	public function paste(Position $anchor){
		foreach($this->blocks as $keyed => $block){
			$anchor->getLevel()->setBlock(self::unkey($keyed)->add($anchor), $block, true, false);
		}
	}
	/**
	 * @return string
	 */
	public function getName(){
		return $this->name;
	}
	/**
	 * @return Block[]
	 */
	public function getBlocks(){
		return $this->blocks;
	}
	/**
	 * @return float
	 */
	public function getCreationTime(){
		return $this->creationTime;
	}
	public function __toString(){
		return $this->name;
	}
}
