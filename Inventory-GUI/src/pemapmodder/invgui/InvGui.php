<?php

namespace pemapmodder\invgui;

use pocketmine\Player;
use pocketmine\item\Item;

abstract class InvGui{
	// $itemId = $invGui->getId() & 0x1FF;
	// $damage = ($invGui->getId() >> 9) & 0x0F;
	public abstract function getId(); // Am I sure each item has a unique ID? Noddy nods, and says, "No, I don't."
	/**
	 * @return array Examples: array(), array($parent->getID()), array($greatGrandparent->getID(), $grandparent->getID(), $parent->getID())
	 */
	public abstract function getInheritance();
	/**
	 * @param Player $player the player who selected the GUI
	 * @return bool whether to restore the invnetory
	 */
	public abstract function onClicked(Player $player);
	public function getPriority(){
		return 0x40;
	}
	public abstract function isParent();
	public function preventHarm(){
		return false;
	}
	public function __toString(){
		$item = self::calcItem($this->getID());
		return get_simple_class_name($this)." ($item)";
	}
	public static final function calcId($id, $meta){
		return ($meta << 9) | ($id & 0x1FF);
	}
	public static final function calcItem($id){
		return Item::get($id & 0x1FF, ($id >> 9) & 0x0F;
	}
}

if(!function_exists("get_simple_class_name")){
	function get_simple_class_name($object){
		return array_slice(explode("\\", get_class($object)), -1);
	}
}
