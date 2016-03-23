<?php

namespace customareas;

use customareas\db\Database;
use customareas\db\DummyDatabase;
use customareas\db\LocalDatabase;
use customareas\pocketmine\EventListener;
use pocketmine\plugin\PluginBase;

class CustomAreas extends PluginBase{
	/** @var Database */
	private $db;

	public function onEnable(){
		$this->saveDefaultConfig();
		$this->initDb();
		$this->getServer()->getPluginManager()->registerEvents(new EventListener($this), $this);
	}

	private function initDb(){
		$type = $this->getConfig()->getNested("database.type");
		switch(strtolower($type)){
			case "mysql":
				/** @noinspection PhpMissingBreakStatementInspection */
			case "mysqli":
				$this->getLogger()->warning("MySQL database is not supported yet! It will be changed into local database.");
			case "local":
			case "nbt":
				$this->setDatabase(new LocalDatabase($this), $this->getConfig()->getNested("database.local"));
				break;
			case "none":
				$this->setDatabase(new DummyDatabase);
				break;
		}
	}

	public function setDatabase(Database $db, $args = null){
		$db->init($args);
		$this->db = $db;
	}

	/**
	 * @return Database
	 */
	public function getDatabase(){
		return $this->db;
	}
}
