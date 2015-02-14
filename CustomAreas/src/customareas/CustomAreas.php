<?php

namespace customareas;

use customareas\db\MySQLDatabase;
use customareas\db\SQLiteDatabase;
use customareas\shape\Shape;
use pocketmine\level\Level;
use pocketmine\plugin\PluginBase;

class CustomAreas extends PluginBase{
	/** @var db\Database */
	private $database;
	/** @var Area[] */
	private $areas = [];
	public function onEnable(){
		$this->saveDefaultConfig();
		if($this->getDatabase() === null){
			return;
		}
		foreach($this->getServer()->getLevels() as $lv){
			$this->loadAreas($lv);
		}
	}
	public function loadAreas(Level $lv){
		$areas = $this->getDatabase()->loadAreas($lv->getName());
		foreach($areas as $area){
			$this->areas[$area->getId()] = $area;
		}
	}
	public function unloadAreas(Level $level){
		$dels = [];
		foreach($this->areas as $k => $area){
			if($area->getLevel()->getName() === $level->getName()){
				$area->validate($this->getDatabase());
				$dels[] = $k;
			}
		}
		foreach($dels as $k){
			unset($this->areas[$k]);
		}
	}
	/**
	 * @return db\Database|null
	 */
	public function getDatabase(){
		if(!isset($this->database)){
			$db = $this->getConfig()->get("database");
			switch($db["type"]){
				case "sqlite":
					$this->database = new SQLiteDatabase($this, $db["sqlite"]["file"]);
					break;
				case "mysql":
					$conn = $db["mysql"];
					$mysqli = new \mysqli(
						$host = $conn["host"],
						$user = $conn["username"],
						$pass = $conn["password"],
						$schema = $conn["schema"],
						$port = $conn["port"]
					);
					if($mysqli->connect_error){
						$this->getLogger()->critical("Could not enable CustomAreas: Could not connect to MySQL database with $user @ $host:$port with default schema $schema");
						// better not log the password
						$this->getServer()->getPluginManager()->disablePlugin($this);
						return null;
					}
					$this->database = new MySQLDatabase($this, $mysqli);
					break;
				default:
					$this->getLogger()->critical("Could not enable CustomAreas: database type " . $db["type"] . " not supported");
					$this->getServer()->getPluginManager()->disablePlugin($this);
					return null;
			}
		}
		return $this->database;
	}
	public function onDisable(){
		if(isset($this->database)){
			$this->database->close();
		}
	}
	public function newArea(Shape $shape, $flags, $owner){
		$id = $this->getDatabase()->nextId();
		$area = new Area($id, $shape, $flags, $owner, true);
	}
}
