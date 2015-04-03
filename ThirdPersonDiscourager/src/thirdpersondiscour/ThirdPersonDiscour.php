<?php

namespace thirdpersondiscour;

use pocketmine\block\Block;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerMoveEvent;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\Player;
use pocketmine\plugin\PluginBase;

class ThirdPersonDiscour extends PluginBase implements Listener{
	/** @var Session[] */
	private $sessions = [];
	/** @var bool */
	private $defaultEnable;
	/** @var Block */
	private $blockType;
	/** @var number */
	private $distance;
	public function onEnable(){
		$this->saveDefaultConfig();
		$this->defaultEnable = $this->getConfig()->get("auto-enable", true);
		$this->blockType = Block::get($this->getConfig()->getNested("block-type.id", 7), $this->getConfig()->getNested("block-type.damage", 0));
		$this->distance = $this->getConfig()->get("block-distance", 2);
		foreach($this->getServer()->getOnlinePlayers() as $p){
			$this->onJoin(new PlayerJoinEvent($p, ""));
		}
	}
	public function onDisable(){
		foreach($this->sessions as $ses){
			$ses->disable();
		}
		$this->sessions = [];
	}
	public function onJoin(PlayerJoinEvent $event){
		$this->sessions[$id = $event->getPlayer()->getId()] = new Session($this, $event->getPlayer());
		if($this->defaultEnable){
			$this->sessions[$id]->enable();
		}
	}
	public function onQuit(PlayerQuitEvent $event){
		if(isset($this->sessions[$id = $event->getPlayer()->getId()])){
			unset($this->sessions[$id]);
		}
	}
	public function onMove(PlayerMoveEvent $event){
		if(isset($this->sessions[$id = $event->getPlayer()->getId()])){
			$this->sessions[$id]->update();
		}
	}
	/**
	 * @return boolean
	 */
	public function isDefaultEnable(){
		return $this->defaultEnable;
	}
	/**
	 * @return Block
	 */
	public function getBlockType(){
		return $this->blockType;
	}
	/**
	 * @return number
	 */
	public function getDistance(){
		return $this->distance;
	}
	public function getSession(Player $player){
		return isset($this->sessions[$id = $player->getId()]) ? $this->sessions[$id] : null;
	}
}
