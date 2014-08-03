<?php

namespace pemapmodder\worldeditart\utils\provider\player;

use pemapmodder\worldeditart\Main;

class MysqliPlayerDataProvider extends SQLPlayerDataProvider{
	/** @var \mysqli */
	private $db;
	public function __construct(Main $main, \mysqli $db){
		$this->db = $db;
		$this->db->query("CREATE TABLE IF NOT EXISTS players (
				name VARCHAR(16) PRIMARY KEY,
				wandidtype TINYINT,
				wandidval TINYINT UNSIGNED,
				wanddamagetype TINYINT,
				wanddamageval TINYINT
				);");
	}
	public function deletePlayerName($name){
		$this->db->query("DELETE FROM players WHERE name = '{$this->db->escape_string($name)}';");
	}
	public function insertPlayerData(array $params){
		$cols = implode(", ", array_keys($params));
		$vals = implode(", ", $params);
		$this->db->query("REPLACE INTO players ($cols) VALUES ($vals);");
	}
	public function selectPlayerFromName($name){
		$result = $this->db->query("SELECT * FROM players WHERE name = '{$this->db->escape_string($name)}';");
		$data = $result->fetch_array(MYSQLI_ASSOC);
		$result->close();
		return $data;
	}
	public function close(){
		$this->db->close();
	}
	public function isAvailable(){
		return $this->db->ping();
	}
}
