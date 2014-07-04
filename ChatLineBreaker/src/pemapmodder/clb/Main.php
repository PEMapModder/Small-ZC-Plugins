<?php

namespace pemapmodder\clb;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\event\Listener;
use pocketmine\event\server\DataPacketSendEvent;
use pocketmine\network\protocol\MessagePacket;
use pocketmine\plugin\PluginBase;

class Main extends PluginBase implements Listener{
	private $testing = [];
	public function onEnable(){
		$this->getServer()->getPluginManager()->registerEvents($this, $this);
	}
	public function onSendPack(DataPacketSendEvent $event){
		$pk = $pk = $event->getPacket();
		if(!($pk instanceof MessagePacket)){
			return;
		}
		$p = $event->getPlayer();
		if($pk->source === "chatlinebreaker.ignore"){
			return;
		}

	}
	public function onCommand(CommandSender $sender, Command $cmd, $alias, array $args){
		switch($cmd = strtolower(array_shift($args))){
			case "set":

				return true;
			case "cal":
			case "calibrate":

				return true;
			case "view":
			case "check":

				return true;
			case "tog":
			case "toggle":

				return true;
			default:
				return false;
		}
	}
	/**
	 * @param string $name
	 * @param int $length
	 */
	public function setLength($name, $length){

	}
	/**
	 * @param string $name
	 */
	public function getLength($name){

	}
	public function onDisable(){
	}
}
