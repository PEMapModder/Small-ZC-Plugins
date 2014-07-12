<?php

namespace pemapmodder\dst;

use pocketmine\block\SignPost;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\event\level\LevelLoadEvent;
use pocketmine\event\level\LevelUnloadEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\IPlayer;
use pocketmine\level\Level;
use pocketmine\math\Vector3;
use pocketmine\Player;
use pocketmine\plugin\PLuginBase;
use pocketmine\tile\Sign;

class Main extends PluginBase implements Listener{
	const SESSION_SIGN_SIGN = 1; // just type SSS if you have autocorrect :P
	const SCROLLTYPE_SCROLL_1 = 1;
	const SCROLLTYPE_SCROLL_2 = 2;
	const SCROLLTYPE_SCROLL_3 = 3;
	const SCROLLTYPE_SCROLL_4 = 4;
	/** @var \SQLite3[] */
	private $dbs = [];
	private $signTouchSessions = [];
	private $ids;
	public function onEnable(){
		$this->getServer()->getPluginManager()->registerEvents($this, $this);
		foreach($this->getServer()->getLevels() as $level){
			$this->openLevelDb($level);
		}
		$this->getServer()->getScheduler()->scheduleDelayedRepeatingTask(new TickUpdater($this), 1, 1);
		$this->saveResource("ids.json");
		$this->ids = json_decode(file_get_contents($this->getDataFolder()."ids.json"));
	}
	public function onDisable(){
		$this->getLogger()->info("Disabling: restoring signs...");
		foreach($this->getServer()->getLevels() as $lv){
			$db = $this->getDb($lv);
			$result = $db->query("SELECT * FROM signs;");
			while(($data = $result->fetchArray(SQLITE3_ASSOC)) !== false){
				$sign = $lv->getTile(new Vector3($data["x"], $data["y"], $data["z"]));
				if($sign instanceof Sign){
					$lengths = $data["lengths"];
					while(strlen($lengths) < 2){
						$lengths .= "\x00";
					}
					$texts = [];
					for($i = 0; $i < 4; $i++){
						$texts[] = TickUpdater::getHalfByteValue($lengths, $i);
					}
					$sign->setText($texts[0], $texts[1], $texts[2], $texts[3]);
				}
				else{
					$db->query("DELETE FROM signs WHERE id = ".$data["id"].";");
				}
			}
			$this->closeLevelDb($lv);
		}
		file_put_contents($this->getDataFolder()."ids.json", json_encode($this->ids, JSON_BIGINT_AS_STRING));
	}
	public function onLvLoad(LevelLoadEvent $event){
		$this->openLevelDb($event->getLevel());
	}
	public function onLvUnlaod(LevelUnloadEvent $event){
		$this->closeLevelDb($event->getLevel());
	}
	private function openLevelDb(Level $level){
		if(isset($this->dbs[$name = $level->getName()])){
			return;
		}
		$this->dbs[$name] = new \SQLite3($this->getDataFolder()."levels/".$level->getName().".sq3");
		$this->dbs[$name]->exec("CREATE TABLE IF NOT EXISTS signs (id INT, x INT, y INT, z INT, ".
			"lengths INT, texts TEXT, intv INT, scroller INT);");
	}
	private function closeLevelDb(Level $level){
		$name = $level->getName();
		if(!isset($this->dbs[$name])){
			$this->getLogger()->notice("Level $name was suspiciously not loaded with Dynamic Signs.");
			return;
		}
		$this->dbs[$name]->close();
		unset($this->dbs[$name]);
	}
	public function onInteract(PlayerInteractEvent $event){
		$b = $event->getBlock();
		if(isset($this->signTouchSessions[$id = $event->getPlayer()->getID()]) and ($b instanceof SignPost)){
			$data = $this->signTouchSessions[$id];
			switch($data["id"]){
				case self::SESSION_SIGN_SIGN:
					$this->signSign($b, $event->getPlayer(), $data["interval"], $data["scroll"]);
					break;
			}
		}
	}
	public function onCommand(CommandSender $issuer, Command $command, $label, array $args){
		switch($command->getName()){
			case "sign":
				if($issuer instanceof Player){
					$interval = 40;
					if(isset($args[0])){
						$interval = floatval($args[0]) * 20;
					}
					$scroll = 2;
					if(isset($args[1])){
						$scroll = intval($args[1]);
					}
					$this->signTouchSessions[$issuer->getID()] = ["id" => self::SESSION_SIGN_SIGN, "interval" => $interval, "scroll" => $scroll];
					$issuer->sendMessage("Please tap on a sign to sign it.");
					return true;
				}
				$issuer->sendMessage("Please run this command in-game.");
				return true;
		}
		return false;
	}
	public function signSign(SignPost $block, IPlayer $player, $interval = 20, $scroll = 2){
		$tile = $block->getLevel()->getTile($block);
		if($tile instanceof Sign){
			$lines = $tile->getText();
			$text = "[DST] This sign was signed by {$player->getName()} at ".date("G:i:s")." on ".date("M j, Y");
			$lines = array_merge($lines, explode("\n", wordwrap($text, 15, "\n")));
			$this->addDS($tile, $lines, $interval, $scroll);
			return true;
		}
		else{
			return false;
		}
	}
	public function addDS(Sign $sign, $lines, $interval = 20, $scroll = 1){
		$lengths = "";
		$clone = $lines;
		if(count($clone) % 2){
			$clone[] = "";
		}
		for($i = 0; $i < count($clone) * 2; $i++){
			$length0 = strlen($clone[$i * 2]);
			$length1 = strlen($clone[$i * 2 + 1]);
			$lengths .= chr($length0 << 4 + $length1);
		}
		$op = $this->getDb($sign->getLevel())->prepare("INSERT INTO signs (id, x, y, z, lengths, " .
			"texts, intv, scroller) VALUES (:id, :x, :y, :z, :lengths, :texts, :intv, :scroller);");
		$op->bindValue(":id", $this->ids["signs"]++);
		$op->bindValue(":x", $sign->x);
		$op->bindValue(":y", $sign->y);
		$op->bindValue(":z", $sign->z);
		$op->bindValue(":lengths", $lengths);
		$op->bindValue(":texts", implode("", $lines));
		$op->bindValue(":intv", $interval);
		$op->bindValue(":scroller", $scroll);
		$op->execute();
	}
	public function getDb(Level $level){
		return isset($this->dbs[$name = $level->getName()]) ? $this->dbs[$name]:false;
	}
}
