<?php

namespace pemapmodder\worldeditart\utils\provider\player;

use pemapmodder\worldeditart\Main;
use pocketmine\item\Item;

class PlayerData{
	const USE_DEFAULT = true;
	const ALLOW_ANY = false;
	/** @var Main */
	private $main;
	/** @var string */
	private $name;
	/** @var int|bool */
	private $wandID, $wandDamage;
	public function __construct(Main $main, $name, $wandID = self::USE_DEFAULT, $wandDamage = self::USE_DEFAULT){
		$this->main = $main;
		$this->name = $name;
		$this->wandID = $wandID;
		$this->wandDamage = $wandDamage;
	}
	/**
	 * @param Item $item
	 * @return bool
	 */
	public function checkWand(Item $item){
		return $this->checkID($item->getID()) and $this->checkDamage($item->getDamage());
	}
	/**
	 * @param $id
	 * @return bool
	 */
	public function checkID($id){
		if($this->wandID === self::ALLOW_ANY){
			return true;
		}
		$wandID = $this->wandID;
		if($this->wandID === self::USE_DEFAULT){
			$wandID = $this->main->getConfig()->get("wand-id");
		}
		return $wandID === $id;
	}
	/**
	 * @param $damage
	 * @return bool
	 */
	public function checkDamage($damage){
		if($this->wandDamage === self::ALLOW_ANY){
			return true;
		}
		$wandDamage = $this->wandDamage;
		if($this->wandDamage === self::USE_DEFAULT){
			$wandDamage = $this->main->getConfig()->get("wand-damage");
		}
		return $wandDamage === $damage;
	}
	/**
	 * @return int|bool
	 */
	public function getWandID(){
		return $this->wandID;
	}
	/**
	 * @return int|bool
	 */
	public function getWandDamage(){
		return $this->wandDamage;
	}
	/**
	 * @param int|bool $wandID
	 */
	public function setWandID($wandID){
		$this->wandID = $wandID;
		$this->main->getPlayerDataProvider()[$this->name] = $this;
	}
	/**
	 * @param int|bool $wandDamage
	 */
	public function setWandDamage($wandDamage){
		$this->wandDamage = $wandDamage;
		$this->main->getPlayerDataProvider()[$this->name] = $this;
	}
	public function __toString(){
		return $this->name;
	}
}
