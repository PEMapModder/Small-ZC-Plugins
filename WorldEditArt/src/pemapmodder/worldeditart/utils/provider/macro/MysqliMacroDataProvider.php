<?php

namespace pemapmodder\worldeditart\utils\provider\macro;

use pemapmodder\worldeditart\WorldEditArt;
use pemapmodder\worldeditart\utils\macro\Macro;
use pemapmodder\worldeditart\utils\macro\MacroOperation;
use pocketmine\block\Block;
use pocketmine\math\Vector3;

class MysqliMacroDataProvider extends CachedMacroDataProvider{
	/** @var \mysqli */
	private $db;
	public function __construct(WorldEditArt $main, \mysqli $db){
		parent::__construct($main);
		$this->db = $db;
		$this->db->query("CREATE TABLE IF NOT EXISTS macros (
				name VARCHAR(65536) PRIMARY KEY,
				author VARCHAR(65536),
				description VARCHAR(65536)
				);");
		$this->db->query("CREATE TABLE IF NOT EXISTS macros_deltas (
				owner VARCHAR(65536),
				offset INT UNSIGNED,
				delta INT UNSIGNED
				);");
		$this->db->query("CREATE TABLE IF NOT EXISTS macros_ops (
				owner VARCHAR(65536),
				offset INT UNSIGNED,
				x BIGINT SIGNED,
				y SMALLINT SIGNED,
				z BIGINT SIGNED,
				id TINYINT UNSIGNED,
				damage TINYINT
				)");
	}
	/**
	 * @return \mysqli
	 */
	public function getDb(){
		return $this->db;
	}
	public function getName(){
		return "MySQLi Macro Data Provider";
	}
	public function readMacro($name){
		$result = $this->getMacroRaw($name);
		$array = $result->fetch_assoc();
		$result->close();
		if(!is_array($array)){
			return null;
		}
		$result = $this->getMacroRaw("macros_deltas", "owner", $name);
		$deltas = $result->fetch_all(MYSQLI_ASSOC);
		$result->close();
		$result = $this->getMacroRaw("macros_ops", "owner", $name);
		$ops = array_merge($deltas, $result->fetch_all(MYSQLI_ASSOC));
		$opers = [];
		foreach($ops as $op){
			if(isset($op["delta"])){
				$opers[$op["offset"]] = new MacroOperation($op["delta"]);
			}
			else{
				$opers[$op["offset"]] = new MacroOperation(new Vector3($op["x"], $op["y"], $op["z"]), Block::get($op["id"], $op["damage"]));
			}
		}
		ksort($opers);
		$macro = new Macro(false, array_values($opers), $array["author"], $array["description"]);
		return $macro;
	}
	public function saveMacro($name, Macro $macro){
		$this->db->query("INSERT INTO macros (name, author, description) VALUES (
				'{$this->db->escape_string($name)}',
				'{$this->db->escape_string($macro->getAuthor())}',
				'{$this->db->escape_string($macro->getDescription())}'
				);");
		foreach($macro->getOperations() as $offset => $op){
			if($op->getType() === MacroOperation::TYPE_WAIT){
				$this->db->query("INSERT INTO macros_deltas (owner, offset, delta) VALUES (
						'{$this->db->escape_string($name)}',
						$offset,
						{$op->getLength()}
						);");
			}
			else{
				$delta = $op->getDelta();
				$block = $op->getBlock();
				$this->db->query("INSERT INTO macros_ops (owner, offset, x, y, z, id, damage) VALUES (
						'{$this->db->escape_string($name)}',
						$offset,
						{$delta->x},
						{$delta->y},
						{$delta->z},
						{$block->getID()},
						{$block->getDamage()}
						);");
			}
		}
	}
	public function deleteMacro($name){
		$escaped = $this->db->escape_string($name);
		$this->db->query("DELETE FROM macros WHERE name = '$escaped';");
		$this->db->query("DELETE FROM macros_deltas WHERE owner = '$escaped';");
		$this->db->query("DELETE FROM macros_ops WHERE owner = '$escaped';");
	}
	/**
	 * Note: remember to close the result
	 *
	 * @param string $table
	 * @param string $column
	 * @param string $name
	 * @return \mysqli_result
	 */
	public function getMacroRaw($table = "macros", $column = "name", $name){
		return $this->db->query("SELECT * FROM $table WHERE $column = '{$this->db->escape_string($name)}';");
	}
	public function isAvailable(){
		return $this->db->ping();
	}
	public function close(){
		$this->db->close();
	}
}
