<?php

namespace pemapmodder\worldeditart\utils\provider\clip;

use pemapmodder\worldeditart\WorldEditArt;
use pemapmodder\worldeditart\utils\clip\Clip;
use pocketmine\block\Block;
use pocketmine\math\Vector3;

class MysqliClipboardProvider extends CachedClipboardProvider{
	/** @var \mysqli */
	private $db;
	public function __construct(WorldEditArt $main, \mysqli $db){
		parent::__construct($main);
		$db->query("CREATE TABLE IF NOT EXISTS clipboard_blocks (
				name VARCHAR(64),
				x BIGINT SIGNED,
				y SMALLINT SIGNED,
				z BIGINT SIGNED,
				id TINYINT UNSIGNED,
				damage TINYINT
				);");
		$this->db = $db;
	}
	public function getClip($name){
		$result = $this->db->query("SELECT * FROM clipboard_blocks WHERE name = '{$this->db->escape_string($name)}';");
		$blocks = [];
		while(is_array($data = $result->fetch_assoc())){
			$blocks[Clip::key(new Vector3($data["x"], $data["y"], $data["z"]))] = Block::get($data["id"], $data["damage"]);
			$rname = $data["name"]; // restore the cases

		}
		$result->close();
		if(!isset($rname)){
			return null;
		}
		$clip = new Clip($blocks, null, $rname);
		return $clip;
	}
	public function setClip($name, Clip $clip){
		if(strlen($name) >= 64){
			throw new \OverflowException("Clip names must not exceed 64 characters!"); // This exception will be caught at SubcommandMap.php
		}
		$blocks = $clip->getBlocks();
		$this->deleteClip($name);
		foreach($blocks as $keyed => $block){
			$unkeyed = Clip::unkey($keyed);
			$this->db->query("INSERT INTO clipboard_blocks VALUES (
					'{$this->db->escape_string($clip->getName())}',
					{$unkeyed->x},
					{$unkeyed->y},
					{$unkeyed->z},
					{$block->getID()},
					{$block->getDamage()}
					);");
		}
	}
	public function deleteClip($name){
		$this->db->query("DELETE FROM clipboard_blocks WHERE name = '{$this->db->escape_string($name)}';");
	}
	public function isAvailable(){
		return $this->db->ping();
	}
	public function close(){
		$this->db->close();
	}
	public function getName(){
		return "MySQLi Clipboard Provider";
	}
}
