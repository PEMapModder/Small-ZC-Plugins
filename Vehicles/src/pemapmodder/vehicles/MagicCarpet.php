<?php

namespace pemapmodder\vehicles;

use pocketmine\block\Block;

class MagicSquareCarpet extends Vehicle{
	/**
	 * @var int $size half length of the carpet
	 */
	protected $size = 5;
	/**
	 * @var int
	 */
	protected $id = 20, $meta = 0;
	public function init(array $args){
		if(isset($args[0]) and is_numeric($args[0])){
			$this->size = (int) $args[0];
		}
		if(isset($args[1])){
			if(is_subclass_of($args[1], "pocketmine\\block\\Block")){
				$class = $args[1];
				/** @var  Block $b */
				$b = new $class();
				$this->id = $b->getID();
				$this->meta = $b->getDamage();
			}
			else{
				if(is_numeric($args[1])){
					$this->id = (int)$args[1];
					$this->meta = 0;
				}
				else{
					$ex = explode(":", $args[1]);
					if(count($ex) === 2){
						$this->id = (int) $ex[0];
						$this->meta = (int) $ex[1];
					}
				}
			}
		}
	}
	public static function getIdentifiers(){
		return array("magiccarpet", "magic-carpet", "magic_carpet", "mc");
	}
	public function getScale(){
		return new VehicleScale($this->size, $this->size, $this->size, $this->size, 0, 1);
	}
	public function getShape(){
		$map = [];
		for($x = 0; $x < $this->size * 2; $x++){
			for($z = 0; $z < $this->size * 2; $z++){
				$map[$x][$z][0] = Block::get($this->id, $this->meta);
			}
		}
		return new VehicleShape($map, $this->getScale());
	}
}