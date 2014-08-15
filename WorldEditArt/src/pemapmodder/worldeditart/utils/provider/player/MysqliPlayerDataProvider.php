<?php

namespace pemapmodder\worldeditart\utils\provider\player;

use pemapmodder\worldeditart\Main;

class MysqliPlayerDataProvider extends SQLPlayerDataProvider{
	/** @var \mysqli */
	private $db;
	public function __construct(Main $main, \mysqli $db){
		parent::__construct($main);
		$this->db = $db;
		$this->db->query("CREATE TABLE IF NOT EXISTS selected_tools (
				player VARCHAR(32),
				tool_id TINYINT,
				item_id_type TINYINT,
				item_id_value SMALLINT,
				item_damage_type TINYINT,
				item_damage_value SMALLINT
				);");
	}
	protected function deletePlayerName($name){
		$this->db->query("DELETE FROM players WHERE name = '{$this->db->escape_string($name)}';");
	}
	protected function insertTool($name, $id, $it, $iv, $dt, $dv){
		$name = $this->escape($name);
		$this->db->query("DELETE FROM selected_tools WHERE player = $name and tool_id = $id;");
		$this->db->query("INSERT INTO selected_tools VALUES ($name, $id, $it, $iv, $dt, $dv);");
	}
	protected function escape($str){
		return "'{$this->db->escape_string($str)}'";
	}
	public function close(){
		$this->db->close();
	}
	public function isAvailable(){
		return $this->db->ping();
	}
	protected function fetchPlayer($name){
		$result = $this->db->query("SELECT * FROM selected_tools WHERE player = {$this->escape($name)};");
		$data = [];
		while(is_array($dat = $result->fetch_assoc())){
			$data[] = $dat;
		}
		return $data;
	}
}
