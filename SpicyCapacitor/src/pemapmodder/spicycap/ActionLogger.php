<?php

namespace pemapmodder\spicycap;

use pocketmine\event\entity\EntityMotionEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerChatEvent;
use pocketmine\Player;

class ActionLogger implements Listener{
	/** @var \SQLite3 */
	private $db;
	/** @var SpicyCap */
	private $main;
	public function __construct(SpicyCap $main){
		$this->main = $main;
		$this->db = new \SQLite3(":memory:");
		$this->db->exec("CREATE TABLE sc_chat (
				tmstmp TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
				player TEXT,
				msg TEXT,
				ip TEXT
		) WITHOUT ROWID;");
		$this->db->exec("CREATE TABLE sc_motion (
				tmstmp TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
				player TEXT,
				x REAL,
				y REAL,
				z REAL,
				x_delta REAL,
				y_delta REAL,
				z_delta REAL,
				ip TEXT
		) WITHOUT ROWID;");
		$main->getServer()->getPluginManager()->registerEvents($this, $main);
	}
	/**
	 * @param PlayerChatEvent $event
	 * @priority MONITOR
	 * @ignoreCancelled true
	 */
	public function onChat(PlayerChatEvent $event){
		$op = $this->db->prepare("INSERT INTO sc_chat (player, msg, ip) VALUES (:player, :msg);");
		$op->bindValue(":player", $event->getPlayer()->getName());
		$op->bindValue(":msg", $event->getMessage());
		$op->bindValue(":ip", $event->getPlayer()->getAddress());
		$op->execute();
	}
	public function onMotion(EntityMotionEvent $event){
		$p = $event->getEntity();
		if($p instanceof Player){
			$op = $this->db->prepare("INSERT INTO sc_motion (player, x, y, z, x_delta, y_delta, z_delta, ip)
				VALUES (:p, :x, :y, :z, :xd, :yd, :zd, :ip);");
			$op->bindValue(":p", $p->getName());
			$op->bindValue(":x", $p->x);
			$op->bindValue(":y", $p->y);
			$op->bindValue(":z", $p->z);
			$v = $event->getVector();
			$op->bindValue(":xd", $v->x);
			$op->bindValue(":yd", $v->y);
			$op->bindValue(":zd", $v->z);
			$op->bindValue(":ip", $p->getAddress());
			$op->execute();
		}
	}
	public function queryChat($player = null, $ip = null, $minLength = 0, $maxLength = 65535){
		$queries = [];
		if($player !== null){
			$queries[] = ["player = :player", ":player", $player];
		}
		if($ip !== null){
			$queries[] = ["ip = :ip", ":ip", $ip];
		}
		if($minLength > 0){
			$queries[] = ["length(msg) >= :min", ":min", $minLength];
		}
		if($maxLength !== 65535){
			$queries[] = ["length(msg) <= :max", ":max", $maxLength];
		}
		if(count($queries) > 0){
			$qs = [];
			foreach($queries as $q){
				$qs[] = $q[0];
			}
			$query = "SELECT * FROM sc_chat WHERE " . implode("AND", $qs) . ";";
			$op = $this->db->prepare($query);
			foreach($queries as $q){
				$op->bindValue($q[1], $q[2]);
			}
			$result = $op->execute();
		}
		else{
			$result = $this->db->query("SELECT * FROM sc_chat;");
		}
		$results = [];
		while(is_array($r = $result->fetchArray(SQLITE3_ASSOC))){
			$results[] = $r;
		}
		return $results;
	}
	public function queryMotion($player = null, $ip = null){
		$queries = [];
		if($player !== null){
			$queries["player"] = $player;
		}
		if($ip !== null){
			$queries["ip"] = $ip;
		}
		if(count($queries) > 0){
			$qs = [];
			foreach($queries as $q){
				$qs[] = $q[0];
			}
			$query = "SELECT * FROM sc_motion WHERE " . implode("AND", $qs) . ";";
			$op = $this->db->prepare($query);
			foreach($queries as $q){
				$op->bindValue($q[1], $q[2]);
			}
			$result = $op->execute();
		}
		else{
			$result = $this->db->query("SELECT * FROM sc_motion;");
		}
		$results = [];
		while(is_array($r = $result->fetchArray(SQLITE3_ASSOC))){
			$results[] = $r;
		}
		return $results;
	}
	public function close(){
		return $this->db->close();
	}
}
