<?php

namespace pemapmodder\worldeditart\utils\provider\player;

use pemapmodder\worldeditart\WorldEditArt;

class SQLite3PlayerDataProvider extends SQLPlayerDataProvider{
	/** @var \SQLite3 */
	private $db;

	public function __construct(WorldEditArt $main, $path){
		parent::__construct($main);
		$this->db = new \SQLite3($main->getDataFolder() . $path);
		$this->db->query("CREATE TABLE IF NOT EXISTS selected_tools (
				player TEXT,
				tool_id INTEGER,
				item_id_type INTEGER,
				item_id_value INTEGER,
				item_damage_type INTEGER,
				item_damage_value INTEGER
				);");
	}

	protected function deletePlayerName($name){
		$op = $this->db->prepare("DELETE FROM players WHERE name = :name;");
		$op->bindValue(":name", strtolower($name));
		$op->execute();
	}

	protected function insertTool($name, $id, $it, $iv, $dt, $dv){
		$name = $this->escape($name);
		$this->db->query("DELETE FROM selected_tools WHERE player = $name and tool_id = $id;");
		$this->db->query("INSERT INTO selected_tools VALUES ($name, $id, $it, $iv, $dt, $dv);");
	}

	protected function escape($str){
		return "'{$this->db->escapeString($str)}'";
	}

	protected function fetchPlayer($name){
		$result = $this->db->query("SELECT * FROM selected_tools WHERE player = {$this->escape($name)};");
		$data = [];
		while(is_array($dat = $result->fetchArray(SQLITE3_ASSOC))){
			$data[] = $dat;
		}
		return $data;
	}

	public function close(){
		$this->db->close();
	}
}
