<?php

namespace pemapmodder\worldeditart\utils\provider\player;

use pemapmodder\worldeditart\Main;

class SQLite3PlayerDataProvider extends SQLPlayerDataProvider{
	/** @var \SQLite3 */
	private $db;
	public function __construct(Main $main, $path){
		parent::__construct($main);
		$this->db = new \SQLite3($path);
		$this->db->query("CREATE TABLE IF NOT EXISTS players (
				name TEXT PRIMARY KEY,
				wandidtype INTEGER,
				wandidval INTEGER,
				wanddamagetype INTEGER,
				wanddamageval INTEGER);");
	}
	protected function selectPlayerFromName($name){
		$op = $this->db->prepare("SELECT * FROM players WHERE name = :name;");
		$op->bindValue(":name", strtolower($name));
		$data = $op->execute()->fetchArray(SQLITE3_ASSOC);
		return $data;
	}
	protected function deletePlayerName($name){
		$op = $this->db->prepare("DELETE FROM players WHERE name = :name;");
		$op->bindValue(":name", strtolower($name));
		$op->execute();
	}
	protected function insertPlayerData(array $params){
		$op = $this->db->prepare("INSERT OR REPLACE INTO players VALUES (".implode(", ", array_keys($params)));
		foreach($params as $param => $value){
			$op->bindValue($param, $value);
		}
		$op->execute();
	}
	public function close(){
		$this->db->close();
	}
}
