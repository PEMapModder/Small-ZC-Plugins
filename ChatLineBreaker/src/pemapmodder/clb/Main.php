<?php

namespace pemapmodder\clb;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\event\Listener;
use pocketmine\event\server\DataPacketSendEvent;
use pocketmine\network\protocol\MessagePacket;
use pocketmine\Player;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\Binary;

class Main extends PluginBase implements Listener{
	private $testing = [];
	/** @var Lang */
	private $lang;
	/** @var Database */
	private $database;
	public function onEnable(){
		$this->getServer()->getPluginManager()->registerEvents($this, $this);
		$this->lang = new Lang($this->getDataFolder()."texts.lang");
		$this->database = new Database($this->getDataFolder()."players.dat");
	}
	public function onSendPack(DataPacketSendEvent $event){
		$pk = $event->getPacket();
		if(!($pk instanceof MessagePacket)){
			return;
		}
		$p = $event->getPlayer();
		if($pk->source === "chatlinebreaker.ignore"){
			return;
		}
		if(!$this->isPEnable($p->getName())){
			return;
		}
		$event->setCancelled();
		$msg = $pk->message;
		if($pk->source){
			$msg = "<{$pk->source}> $msg";
		}
		$this->processMessage($p, $msg);
	}
	private function processMessage(Player $player, $message){

	}
	public function onCommand(CommandSender $sender, Command $cmd, $alias, array $args){
		if(!isset($args[0])) return false;
		switch($cmd = strtolower(array_shift($args))){
			case "set":
				if(!isset($args[0])){
					return true;
				}
				$l = array_shift($args);
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
	 * @return int
	 */
	public function getLength($name){

	}
	/**
	 * @param string $name
	 * @param bool $value
	 */
	public function setPEnable($name, $value){

	}
	/**
	 * @param $name
	 * @return bool
	 */
	public function isPEnable($name){

	}
	private function schedule($ticks, callable $callback, array $data, $isRepeating = false){
		$task = new CallbackTask($this, $callback, $data);
		$s = $this->getServer()->getScheduler();
		if($isRepeating){
			$s->scheduleRepeatingTask($task, $ticks);
		}
		else{
			$s->scheduleDelayedTask($task, $ticks);
		}
	}
	public static function compressName($name){
		$name = strtolower($name); // [0-9a-z_]{3,16}
		$output = 0;
		for($offset = 0; $offset < strlen($name); $offset++){
			$order = ord(substr($name, $offset, 1));
			$zero = ord("0");
			$alpha = ord("a");
			$ord = $order - $zero;
			$alphaOrd = $order - $alpha;
			if(0 <= $ord and $ord <= 9){
				$output += $ord;
			}
			elseif(0 <= $alphaOrd and $alphaOrd <= 25){
				$output += ($alphaOrd + 10);
			}
			elseif($order === ord("_")){
				$output += 36;
			}
			$output <<= 6;
		}
		$out = Binary::writeInt($output & 0xFFFFFFFF);
		$output >>= 32;
		$out = Binary::writeLong($output).$out;
		return $out;
	}
	public static function decompressName($compressed){
		$int = substr($compressed, 0, 4);
		$long = substr($compressed, 4, 8);
		/** @var int $value */
		$value = Binary::readInt($int, false);
		$value <<= 32;
		$value += Binary::readLong($long, false);
		$out = "";
		for($offset = 0; $offset <= 16; $offset++){
			$ord = $value & 0b111111;
			if(0 <= $ord and $ord <= 9){
				$chr = chr(ord("0") + $ord);
			}
			else{
				$ord -= 10;
				if($ord <= 25){
					$chr = chr(ord("a") + $ord);
				}
				else $chr = "_"; // corrupted databases will lead to a large amount of underscores
			}
			$out = $chr.$out;
		}
		return $out;
	}
}
