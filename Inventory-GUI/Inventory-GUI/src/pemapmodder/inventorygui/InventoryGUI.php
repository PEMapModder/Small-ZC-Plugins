<?php

namespace pemapmodder\inventorygui;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\event\Listener;
use pocketmine\inventory\InventoryHolder;
use pocketmine\Player;
use pocketmine\plugin\PluginBase;

class InventoryGUI extends PluginBase implements Listener, InventoryHolder{
	/** @var GUIInventory */
	private $inventory;
	/** @var GUI[] */
	private $guis = [];
	public function onEnable(){
		$this->getServer()->getScheduler()->scheduleDelayedTask(new InitTask($this), 1);
	}
	public function initialize(){
		$this->getServer()->getPluginManager()->registerEvents($this, $this);
		$this->initInventory();
	}
	public function registerGUI(GUI $gui){
		$this->guis[] = $gui;
		$this->recalculatePriorities();
	}
	public function recalculatePriorities(){
		$guis = [];
		$priorities = [];
		foreach($this->guis as $gui){
			$guis[spl_object_hash($gui)] = $gui;
			$priorities[spl_object_hash($gui)] = $gui->getPriority();
		}
		rsort($priorities);
		$this->guis = [];
		foreach($priorities as $key => $p){
			$this->guis[] = $priorities[$key];
		}
	}
	public function onCommand(CommandSender $issuer, Command $cmd, $alias, array $args){
		if(!($issuer instanceof Player)){
			$issuer->sendMessage("Please run this command in-game.");
			return true;
		}
		$this->openGUI($issuer);
		return true;
	}
	public function openGUI(Player $player){
		$this->getInventory()->open($player);
	}
	private function initInventory(){
		$this->inventory = new GUIInventory($this);
	}
	public function getInventory(){
		return $this->inventory;
	}
}
