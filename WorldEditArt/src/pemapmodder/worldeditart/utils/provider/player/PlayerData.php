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
	private $wandID, $wandDamage, $jumpID, $jumpDamage;
	public function __construct(Main $main, $name, $wandID = self::USE_DEFAULT, $wandDamage = self::USE_DEFAULT, $jumpID = self::USE_DEFAULT, $jumpDamage = self::USE_DEFAULT){
		$this->main = $main;
		$this->name = $name;
		$this->wandID = $wandID;
		$this->wandDamage = $wandDamage;
		$this->jumpID = $jumpID;
		$this->jumpDamage = $jumpDamage;
	}
	// wand
	/**
	 * @param Item $item
	 * @return bool
	 */
	public function checkWand(Item $item){
		return $this->checkWandID($item->getID()) and $this->checkWandDamage($item->getDamage());
	}
	/**
	 * @param $id
	 * @return bool
	 */
	public function checkWandID($id){
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
	public function checkWandDamage($damage){
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
		$this->update();
	}
	/**
	 * @param int|bool $wandDamage
	 */
	public function setWandDamage($wandDamage){
		$this->wandDamage = $wandDamage;
		$this->update();
	}
	// jump
	public function checkJump(Item $item){
		return $this->checkJumpID($item->getID()) and $this->checkJumpDamage($item->getDamage());
	}
	public function checkJumpID($id){
		if($this->jumpID === self::ALLOW_ANY){
			return true;
		}
		$required = $this->jumpID;
		if($required === self::USE_DEFAULT){
			$required = $this->main->getConfig()->get("jump-id");
		}
		return $id === $required;
	}
	public function checkJumpDamage($damage){
		if($this->jumpDamage === self::ALLOW_ANY){
			return true;
		}
		$required = $this->jumpDamage;
		if($required === self::USE_DEFAULT){
			$required = $this->main->getConfig()->get("jump-damage");
		}
		return $damage === $required;
	}
	/**
	 * @return bool|int
	 */
	public function getJumpID(){
		return $this->jumpID;
	}
	/**
	 * @param bool|int $jumpID
	 */
	public function setJumpID($jumpID){
		$this->jumpID = $jumpID;
		$this->update();
	}
	/**
	 * @return bool|int
	 */
	public function getJumpDamage(){
		return $this->jumpDamage;
	}
	/**
	 * @param bool|int $jumpDamage
	 */
	public function setJumpDamage($jumpDamage){
		$this->jumpDamage = $jumpDamage;
		$this->update();
	}
	public function update(){
		$this->main->getPlayerDataProvider()[$this->name] = $this;
	}
	public function __toString(){
		return $this->name;
	}
}
