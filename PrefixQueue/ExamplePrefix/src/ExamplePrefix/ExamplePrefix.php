<?php

namespace ExamplePrefix;

use pocketmine\event\Listener;
use pocketmine\event\player\PlayerChatEvent;
use pocketmine\event\plugin\PluginEnableEvent;
use pocketmine\Player;
use pocketmine\plugin\PluginBase;
use PrefixQueue\PrefixQueue;

class ExamplePrefix extends PluginBase implements Listener{
	private $registeredToAPI = false;
	private $registered = false;
	private $manualPrefix = false;
	public function onEnable(){
		if(class_exists("PrefixQueue\\PrefixQueue")){
			$this->registerToAPI();
		}
		else{
			$this->onNoAPI();
		}
	}
	public function getPlayerPrefix(Player $player){
		if($player->isOp()){
			return "[OP]";
		}
		return "";
	}
	public function onNoAPI(){
		$this->registeredToAPI = false;
		$this->manualPrefix = true;
		$this->registerEvents();
	}
	private function registerEvents(){
		if($this->registered === true){
			return;
		}
		$this->registered = true;
		$this->getServer()->getPluginManager()->registerEvents($this, $this);
	}
	public function onChat(PlayerChatEvent $event){
		$event->setFormat($this->getPlayerPrefix($event->getPlayer()) . " " . $event->getFormat());
	}
	public function onAPIEnabled(PluginEnableEvent $event){
		if(get_class($event->getPlugin()) === "PrefixQueue\\PrefixQueue"){
			$this->registerToAPI();
		}
	}
	private function registerToAPI(){
		if($this->registeredToAPI){
			return;
		}
		$this->registeredToAPI = true;
		PrefixQueue::register($this, array($this, "getPlayerPrefix"), 50, array($this, "onNoAPI"));
	}
}
