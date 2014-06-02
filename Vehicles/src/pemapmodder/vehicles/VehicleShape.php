<?php

namespace pemapmodder\vehicles;

use pocketmine\block\Block;
use pocketmine\level\Level;
use pocketmine\level\Position;

class VehicleShape{
	/**
	 * @var \pocketmine\block\Block[][][] keyed with $map[$x][$z][$y]
	 */
	protected $map;
	/**
	 * @var VehicleScale
	 */
	protected $scale;
	public function __construct(array $blockMap, VehicleScale $scale){
		$this->map = $blockMap;
		$this->scale = $scale;
	}
	/**
	 * @param Level $level
	 * @return Block[]
	 */
	public function getSimpleMap(Level $level){
		$out = [];
		foreach($this->map as $x=>$data0){
			foreach($data0 as $z=>$data1){
				foreach($data1 as $y=>$block){
					if($block instanceof Block){
						$out[] = Block::get($block->getID(), $block->getDamage(), new Position($x, $y, $z, $level));
					}
				}
			}
		}
		return $out;
	}
	/**
	 * @param Position $pos
	 * @return \pocketmine\block\Block[]
	 */
	public function getMapAt(Position $pos){
		$simple = $this->getSimpleMap($pos->getLevel());
		$out = [];
		foreach($simple as $block){
			$b = $block->add($pos->getFloorX(), $pos->getFloorY(), $pos->getFloorZ());
			$out[] = new Position($b->getFloorX(), $b->getFloorY(), $b->getFloorZ());
		}
		return $out;
	}
}
