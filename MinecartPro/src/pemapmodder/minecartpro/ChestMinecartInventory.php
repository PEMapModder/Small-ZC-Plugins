<?php

namespace pemapmodder\minecartpro;

use pocketmine\inventory\CustomInventory;
use pocketmine\inventory\InventoryType;
use pocketmine\item\Item;
use pocketmine\nbt\tag\ByteArray;
use pocketmine\nbt\tag\Enum;

class ChestMinecartInventory extends CustomInventory{
	public function __construct(Enum $enum, ChestMinecart $holder){
		parent::__construct($holder, InventoryType::get(InventoryType::CHEST));
		for($i = 0; isset($enum[$i]); $i++){
			/** @var \pocketmine\nbt\tag\ByteArray $item */
			$item = $enum[$i];
			$item = $item->getValue();
			$this->setItem($i, Item::get(ord($item{0}), ord($item{1}), ord($item{2})));
		}
	}
	public function saveTo(Enum $enum){
		$tag = new Enum;
		for($i = 0; $i < 27; $i++){
			$item = new ByteArray;
			$item->setValue(chr($this->getItem($i)->getID()).
					chr($this->getItem($i)->getDamage()).
					chr($this->getItem($i)->getCount()));
			$tag[$i] = $item;
		}
		$i = -1;
		while(true){
			$i++;
			if(!isset($enum[$i])){
				break;
			}
		}
		$enum[$i] = $tag;
	}
}
