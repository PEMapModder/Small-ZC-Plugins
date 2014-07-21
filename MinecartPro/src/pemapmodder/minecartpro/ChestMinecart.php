<?php

namespace pemapmodder\minecartpro;

use pocketmine\entity\Minecart;
use pocketmine\inventory\InventoryHolder;

class ChestMinecart implements InventoryHolder{
	private $id;
	private $main;
	public function __construct(Minecart $minecart, Main $main, $id){
		$this->id = $id;
		$this->main = $main;
		$minecart->setMetadata("minecartpro.chest", new MinecartMetaValue($main, $this->id));
		@$main->addEmptyInventory($this->id, $this);
	}
	public function getInventory(){
		return $this->main->getInventory($this->id);
	}
}
