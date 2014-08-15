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
	 */
	public function __construct($id, $damage, $defaultID, $defaultDamage){
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
