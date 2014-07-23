<?php

namespace pemapmodder\inventorygui;

use pocketmine\inventory\CustomInventory;
use pocketmine\inventory\InventoryType;

class GUIInventory extends CustomInventory{
	protected $main;
	public function __construct(InventoryGUI $plugin){
		$this->main = $plugin;
		parent::__construct($plugin, InventoryType::get(InventoryType::DOUBLE_CHEST), [], 108, "Inventory-GUI");
	}
	public function getPlugin(){
		return $this->main;
	}
}
