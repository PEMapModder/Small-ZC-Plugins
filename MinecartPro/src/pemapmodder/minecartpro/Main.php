<?php

namespace pemapmodder\minecartpro;

use pocketmine\entity\Minecart;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\Listener;
use pocketmine\item\Item;
use pocketmine\nbt\NBT;
use pocketmine\nbt\tag\Enum;
use pocketmine\Player;
use pocketmine\plugin\PluginBase;

class Main extends PluginBase implements Listener{
	private $saved;
	private $inventoryHolders = [];
	private $inventories = [];
	public function onEnable(){
		$this->getServer()->getPluginManager()->registerEvents($this, $this);
		@mkdir($this->getDataFolder());
		$this->saved = file_exists($path = $this->getDataFolder()."saved-values.json") ?
			json_decode(file_get_contents($path)):["next-id" => 0];
		$path = file_get_contents($this->getDataFolder()."inventories.dat");
		if(is_file($path)){
			$nbt = new NBT;
			$nbt->readCompressed($path);
			/** @var \pocketmine\nbt\tag\Enum $inventories */
			$inventories = $nbt->getData();
			for($i = 0; isset($inventories[$i]); $i++){
				$mob = null;
				if(!($mob instanceof Minecart)){
					continue;
				}
				$this->inventories[$i] = new ChestMinecartInventory($inventories[$i], $this->inventoryHolders[$i] = new ChestMinecart(null, $this, $i));
			}
		}
	}
	public function onDisable(){
		file_put_contents($this->getDataFolder()."saved-values.json", json_encode($this->saved));
		$nbt = new NBT;
		file_put_contents($this->getDataFolder()."inventories.dat", $nbt->writeCompressed());
	}
	public function nextID(){
		return $this->saved["next-id"]++;
	}
	/**
	 * @param $id
	 * @return ChestMinecartInventory
	 */
	public function getInventory($id){
		return $this->inventories[$id];
	}
	public function attackHook(EntityDamageByEntityEvent $event){
		$hit = $event->getEntity();
		$hitter = $event->getDamager();
		if(($hitter instanceof Player) and ($hit instanceof Minecart)){
			if($hitter->hasPermission("minecartpro.chest.use") and $hitter->getInventory()->getItemInHand()->getID() === Item::CHEST){
				if($hit->hasMetadata("minecartpro.chest")){
					$metadata = $hit->getMetadata("minecartpro.chest");
					/** @var $meta \pocketmine\metadata\MetadataValue */
					foreach($metadata as $meta){
						if($meta->getOwningPlugin()->getName() === $this->getName()){
							$id = $meta->value();
						}
					}
					if(!isset($id)){
						goto create;
					}
					/** @var ChestMinecart $inventory */
					$inventory = $this->inventoryHolders[$id];
				}
				else{
					create:
					$id = $this->nextID();
					$this->inventoryHolders[$id] = new ChestMinecart($hit, $this, $id);
					/** @var ChestMinecart $inventory */
					$inventory = $this->inventoryHolders[$id];
				}
				$inv = $inventory->getInventory();
				$inv->sendContents($hitter);
			}
		}
	}
	public function addEmptyInventory($id, ChestMinecart $holder){
		$this->inventories[$id] = new ChestMinecartInventory(new Enum, $holder);
	}
}
