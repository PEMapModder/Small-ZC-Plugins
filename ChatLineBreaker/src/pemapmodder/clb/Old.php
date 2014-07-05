<?php

namespace pemapmodder\clb;

use pocketmine\event\Listener;
use pocketmine\event\player\PlayerCommandPreprocessEvent;
use pocketmine\event\server\DataPacketSendEvent;
use pocketmine\network\protocol\MessagePacket;
use pocketmine\Player;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\Binary;

class ChatLineBreaker extends PluginBase implements Listener{
	const MAGIC_PREFIX = "\x00\xffCLBDB>";
	const MAGIC_SUFFIX = "=CLBDB\xff\x00";
	const CORRUPTION_PREFIX = "prefix";
	const CORRUPTION_SUFFIX = "suffix";
	const CORRUPTION_API = "unsupported api";
	const CURRENT_VERSION = "\x01";
	const INITIAL_RELEASE = "";
	const DB_LANG_UPDATE = "\x01";
	public $database = array();
	public $testing = array();
	public $config; // made them public to thank the public Player::$data :)
	public $path, $cfgPath, $langPath;
	/** @var Lang */
	public $lang;
	public function onEnable(){
		$time = microtime(true);
		echo ".";
		$this->path = $this->getDataFolder()."players.dat";
		$this->cfgPath = $this->getDataFolder()."config.";
		$this->saveDefaultConfig();
		$this->langPath = $this->getDataFolder()."texts.lang";
		$this->lang = new Lang($this->langPath);
		echo ".";
		$this->load();
		$time *= -1;
		$time += microtime(true);
		$time *= 1000;
		echo " Done! ($time ms)".PHP_EOL;
	}
	public function eventSetLength($data){
		if(!isset($data["cid"]) or !isset($data["length"])){
			return false;
		}
		$this->setLength($data["cid"], $data["length"]);
		return true;
	}
	public function eventSetEnabled($data){
		if(!isset($data["cid"]) or !isset($data["bool"])){
			return false;
		}
		$this->setEnabled($data["cid"], $data["bool"]);
		return true;
	}
	public function onChat(PlayerCommandPreprocessEvent $event){
		$p = $event->getPlayer();
		if(!in_array($p->getID(), $this->testing)){
			return;
		}
		$msg = $event->getMessage();
		$cid = $p->loginData["clientId"];
		if(!is_numeric($msg)){
			$p->sendMessage($this->lang["calibrate.response.not.numeric"]);
			$event->setCancelled();
			return;
		}
		$l = (int) $msg;
		$issuer = $p; // such a lazy fix... :P
		if($l <= 5){
			$issuer->sendMessage(str_replace("@char", "$l", $this->lang["calibrate.response.too.low"]));
			$event->setCancelled();
			return;
		}
		if($l >= 0b10000000){
			$issuer->sendMessage(str_replace("@char", "$l", $this->lang["calibrate.response.too.high"]));
			$event->setCancelled();
			return;
		}
		$this->setLength($cid, $l);
		$p->sendMessage(str_replace("@char", "$l", $this->lang["calibrate.response.succeed"]));
		unset($this->testing[array_search($p->getID(), $this->testing)]);
		$event->setCancelled();
		return;
	}
	public function onQuit($p){
		if(in_array($p->CID, $this->testing)){
			unset($this->testing[array_search($p->CID, $this->testing)]);
		}
	}
	public function onSend(DataPacketSendEvent $evt){
		if(!(($pk = $evt->getPacket()) instanceof MessagePacket)){
			return;
		}
		if($evt->getPacket()->source === "clb.followup.linebreak"){
			$evt->getPacket()->source = "";
			return;
		}
		$packets = $this->processMessage($evt->getPlayer()->data->get("lastID"), $pk->message, $evt->getPlayer()); // thanks for making it public property, shoghicp!
		if($packets === false)
			return;
		// I made it use client ID because the line break length should depend on the device not the player or IP
		$evt->setCancelled(true);
		foreach($packets as $pk){
			$evt->getPlayer()->dataPacket($pk);
		}
		if(defined("DEBUG") and DEBUG >= 2){
			// var_export($pk);
		}
	}
	public function onCmd($cmd, $args, $issuer){
		if($issuer === "console"){
			return $this->lang["cmd.console.reject"]; // lol
		}
		if($issuer === "rcon"){
			return "Did you expect we can modify your RCon client preferences for you? We are not hackers!"; // lol * 2
		}
		$cmd = array_shift($args);
		$output = "[CLB] ";
		$cid = $issuer->data->get("lastID");
		switch($cmd){
			case "cal":
			case "calibrate":
				$msgs = $this->getTesterMessage();
				$output .= array_shift($msgs);
				foreach($msgs as $key=>$value){
					$this->api->schedule(40 * ($key + 1), array($issuer, "sendChat"), $value, false, "ChatLineBreaker"); // why did you add this 5th arg...
				}
				$this->testing[] = $issuer->CID;
				break;
			case "set":
				$l = (int) array_shift($args);
				if($l <= 5){
					$output .= $this->lang["calibrate.response.not.numeric"]."\n";
					break;
				}
				if($l >= 0b10000000){
					$output .= str_replace("@char", "$l", $this->lang["calibrate.response.too.low"])."\n";
					break;
				}
				$this->setLength($cid, $l);
				$output .= "Your CLB length is now $l.\n";
				break;
			case "check":
			case "view":
				$l = $this->getLength($cid);
				$output .= $this->lang["view.".($this->isEnabled($cid) ? "on":"off")];
				$output .= str_replace("@length", "$l", $this->lang["view.length"]);
				break;
			case "tog":
			case "toggle":
				$this->setEnabled($cid, ($b = !$this->isEnabled($cid)));
				$output .= $this->lang["toggle.".($b ? "on":"off")];
				break;
			case "help":
				$output .= "Showing help for /clb\n";
			default:
				$output .= "\"/clb\" ChatLineBreaker (CLB) settings panel.\n";
				$output .= "CLB is a tool for breaking chat lines into pieces automatically to suit your device length.\n";
				$output .= "\"/clb cal\" or \"/clb calibrate\": Use the CLB linebreak tester to calibrate your CLB length.\n";
				$output .= "\"/clb set <length>\": (Not recommended) Set your CLB length to the defined length.\n";
				$output .= "\"/clb view\" or \"/clb check\" to check if CLB is enabled for you.\n";
				$output .= "\"/clb tog\" or \"/clb toggle\" to toggle your CLB.\n";
		}
		if(defined("DEBUG") and DEBUG >= 2){
			var_export($output);
		}
		return $output;
	}
	public function getLength($cid){
		if(isset($this->database[$cid])){
			return $this->database[$cid][1];
		}
		return $this->config->get("default-length");
	}
	public function setLength($cid, $length){
		$this->database[$cid] = array($this->isEnabled($cid), $length);
	}
	public function isEnabled($cid){
		if(isset($this->database[$cid])){
			return $this->database[$cid][0];
		}
		return $this->config->get("default-enable");
	}
	public function setEnabled($cid, $bool){
		$this->database[$cid] = array($bool, $this->getLength($cid));
	}
	public function getTesterMessage(){
		$numbers = "";
		for($i = 1; $i < 10; $i++){
			$numbers .= "$i";
		}
		for($i = 11; $i < 100; $i+= 3){
			$numbers .= "$i,";
		}
		return array($this->lang["calibrate.instruction.close.screen"],
			$this->lang["calibrate.instruction.next.message"],
			$numbers,
			$this->lang["calibrate.instruction.hyphens.separator"],
			$this->lang["calibrate.instruction.ask.number"],
			$this->lang["calibrate.instruction.require.type.chat"]);
	}
	public function getData($cid){
		return $this->database[$cid];
	}
	public function processMessage($clientID, $message, Player $p){
		if(!$this->isEnabled($clientID)){
			return false;
		}
		$wrapped = explode("\n", wordwrap($message, $this->getLength($clientID), "\n"));
		if(count($wrapped) === 1){
			return false;
		}
		$packets = array();
		foreach($wrapped as $wrap){
			$pk = new MessagePacket;
			$pk->source = "clb.followup.linebreak";
			$pk->message = $wrap;
			$packets[] = $pk;
		}
		return $packets;
	}
	public function save(){
		$this->getLogger()->debug("Saving CLB database...");
		$time = microtime(true);
		$buffer = self::MAGIC_PREFIX;
		$buffer .= self::CURRENT_VERSION;
		foreach($this->database as $cid=>$data){
			$buffer .= Binary::writeLong($cid);
			$ascii = $data[1];
			if($data[0]){
				$ascii |= 0b10000000;
			}
			$buffer .= chr($ascii);
		}
		$buffer .= self::MAGIC_SUFFIX;
		file_put_contents($this->path, $buffer, LOCK_EX);
		$this->getLogger()->debug("Done!", true, true, 2);
	}
	public function load(){
		$this->getLogger()->debug("Loading CLB database...");
		$time = 0 - microtime(true);
		$str = @file_get_contents($this->path);
		if($str === false){
			$this->save();
			return true;
		}
		$isOld = (strlen($str) % 9) === 0;
		if(!$isOld){
			if(substr($str, 0, strlen(self::MAGIC_PREFIX)) !== self::MAGIC_PREFIX){
				// TODO handle missing prefix database corruption
				$this->database = array();
				$this->save();
				trigger_error("CLB database corrupted. Component corrupted: ".self::CORRUPTION_PREFIX, E_USER_WARNING);
			}
			if(substr($str, -1 * strlen(self::MAGIC_SUFFIX)) !== self::MAGIC_SUFFIX){
				// TODO handle missing suffix database corruption
				$this->database = array();
				$this->save();
				trigger_error("CLB database corrupted. Component corrupted: ".self::CORRUPTION_SUFFIX, E_USER_WARNING);
			}
			$str = substr($str, strlen(self::MAGIC_PREFIX), -1 * strlen(self::MAGIC_SUFFIX));
			$api = substr($str, 0, 1);
			if($api > self::CURRENT_VERSION){
				// TODO handle incorrect API database corruption
				$this->database = array();
				$this->save();
				trigger_error("CLB database corrupted. Component corrupted: ".self::CORRUPTION_API, E_USER_WARNING);
			}
			$str = substr($str, 1);
		}
		for($i = 0; $i < strlen($str); $i+= 9){
			$cur = substr($str, $i, 9);
			$key = Binary::readLong(substr($cur, 0, 8));
			$number = ord(substr($str, 8));
			$bool = ($number & 0b10000000) !== 0;
			$length = $number & 0b01111111;
			$this->database[$key] = array($bool, $length);
		}
		$time += microtime(true);
		$time *= 1000;
		$this->getLogger()->debug("Done! ($time ms)", true, true, 2);
		return false;
	}
	public function onDisable(){
		$this->save();
	}
}

