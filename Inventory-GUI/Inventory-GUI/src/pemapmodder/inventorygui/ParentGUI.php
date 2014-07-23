<?php

namespace pemapmodder\inventorygui;

use pocketmine\inventory\CustomInventory;
use pocketmine\inventory\InventoryType;
use pocketmine\item\Item;
use pocketmine\Player;
use pocketmine\Server;

abstract class ParentGUI extends CustomInventory implements GUI{
	protected $server;
	public function __construct(Server $server){
		$this->server = $server;
		/** @var InventoryGUI $main */
		$main = $server->getPluginManager()->getPlugin("Inventory-GUI");
		parent::__construct($main, InventoryType::get(InventoryType::DOUBLE_CHEST), array_map(function($gui){
			/** @var GUI $gui */
			return Item::get($gui->getID(), $gui->getDamage());
		}, $this->getChildren()), count($this->getChildren()), $this->getMenuName());
	}
	/**
	 * @return string
	 */
	public function getMenuName(){
		return "Inventory-GUI";
	}
	/**
	 * @return GUI[]
	 */
	public abstract function getChildren();
	/**
	 * @return CustomInventory
	 */
	public function getParent(){
		/** @var InventoryGUI $main */
		$main = $this->server->getPluginManager()->getPlugin("Inventory-GUI");
		return $main->getInventory();
	}
	public function onActivation(Player $player){
		$this->getParent()->close($player);
		$this->open($player);
	}
}
