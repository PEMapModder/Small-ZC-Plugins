<?php

namespace pemapmodder\worldeditart\utils\provider\player;

use pocketmine\item\Item;

class SelectedTool{
	/** @var bool|int */
	private $id;
	/** @var bool|int */
	private $damage;
	/** @var int */
	private $defaultID;
	/** @var bool|int */
	private $defaultDamage;
	/**
	 * @param int|bool $id
	 * @param int|bool $damage
	 * @param int $defaultID
	 * @param int|bool $defaultDamage
	 * @throws \InvalidArgumentException
	 */
	public function __construct($id, $damage, $defaultID, $defaultDamage){
		$path = "pocketmine\\DEBUG";
		if(defined($path) and constant($path) > 1){
			foreach(func_get_args() as $arg){
				if(!is_int($arg) and !is_bool($arg)){
					throw new \InvalidArgumentException("Invalid arguments passed to SelectedTool constructor!");
				}
			}
		}
		$this->id = $id;
		$this->damage = $damage;
		$this->defaultID = $defaultID;
		$this->defaultDamage = $defaultDamage;
	}
	public function match(Item $item){
		return $this->matchRaw($this->id, $item->getID(), $this->defaultID) and $this->matchRaw($this->damage, $item->getDamage(), $this->defaultDamage);
	}
	public function matchRaw($comparator, $subject, $default){
		if($comparator === PlayerData::USE_DEFAULT){
			$comparator = $default;
		}
		if($comparator === PlayerData::ALLOW_ANY){
			return true;
		}
		return $comparator === $subject;
	}
	public function getRawID(){
		return $this->id;
	}
	public function getRawDamage(){
		return $this->damage;
	}
}
