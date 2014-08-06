<?php

namespace pemapmodder\worldeditart\utils\provider\player;

abstract class SQLPlayerDataProvider extends PlayerDataProvider{
	const TRUE = 1;
	const FALSE = 0;
	const INT = 2;
	/**
	 * @param string $name
	 * @return array
	 */
	protected abstract function selectPlayerFromName($name);
	/**
	 * @param string $name
	 */
	protected abstract function deletePlayerName($name);
	/**
	 * @param string[] $params
	 */
	protected abstract function insertPlayerData(array $params);
	public function offsetGet($name){
		$data = $this->selectPlayerFromName($name);
		if(!is_array($data)){
			return new PlayerData($this->getMain(), $name);
		}
		$id = $data["wandidval"];
		$type = $data["wandidtype"];
		if($type !== self::INT){
			$id = (bool) $type;
		}
		$damage = $data["wanddamageval"];
		$type = $data["wanddamagetype"];
		if($type !== self::INT){
			$id = (bool) $type;
		}
		$jumpid = $data["jumpidval"];
		$type = $data["jumpidtype"];
		if($type !== self::INT){
			$jumpid = (bool) $type;
		}
		$jumpdamage = $data["jumpdamageval"];
		$type = $data["jumpdamagetype"];
		if($type !== self::INT){
			$jumpdamage = (bool) $type;
		}
		return new PlayerData($this->getMain(), $name, $id, $damage, $jumpid, $jumpdamage);
	}
	public function offsetSet($name, $data){
		if(!($data instanceof PlayerData)){
			throw new \InvalidArgumentException("Player data passed to FilePlayerDataProvider must be instance of PlayerData, ".
				(is_object($data) ? get_class($data):gettype($data))." given");
		}
		$params = [];
		$params[":name"] = $name;
		$params[":wandidtype"] = is_bool($data->getWandID()) ? ($data->getWandID() ? self::TRUE:self::FALSE):self::INT;
		$params[":wandidval"] = (int) $data->getWandID();
		$params[":wanddamagetype"] = is_bool($data->getWandDamage()) ? ($data->getWandDamage() ? self::TRUE:self::FALSE):self::INT;;
		$params[":wanddamageval"] = (int) $data->getWandDamage();
		$params[":jumpidtype"] = is_bool($data->getJumpID()) ? ($data->getJumpID() ? self::TRUE:self::FALSE):self::INT;
		$params[":jumpidval"] = (int) $data->getJumpID();
		$params[":jumpdamagetype"] = is_bool($data->getJumpDamage()) ? ($data->getJumpDamage() ? self::TRUE:self::FALSE):self::INT;;
		$params[":jumpdamageval"] = (int) $data->getJumpDamage();
		$this->insertPlayerData($params);
	}
	public function offsetUnset($name){
		$this->deletePlayerName($name);
	}
	public function getName(){
		return "SQLite3 Player Data Provider";
	}
	public function isAvailable(){
		return true;
	}
}
