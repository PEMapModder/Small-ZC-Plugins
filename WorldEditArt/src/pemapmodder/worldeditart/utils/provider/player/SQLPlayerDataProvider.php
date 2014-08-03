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
			$id = $type;
		}
		$damage = $data["wanddamageval"];
		$type = $data["wanddamagetype"];
		if($type !== self::INT){
			$id = $type;
		}
		return new PlayerData($this->getMain(), $name, $id, $damage);
	}
	public function offsetSet($name, $data){
		if(!($data instanceof PlayerData)){
			throw new \InvalidArgumentException("Player data passed to FilePlayerDataProvider must be instance of PlayerData, ".
				(is_object($data) ? get_class($data):gettype($data))." given");
		}
		$params = [];
		$idType = self::INT;
		if(is_bool($data->getWandID())){
			$idType = $data->getWandID() ? self::TRUE:self::FALSE;
		}
		$idVal = (int) $data->getWandID();
		$damageType = self::INT;
		if(is_bool($data->getWandDamage())){
			$damageType = $data->getWandDamage() ? self::TRUE:self::FALSE;
		}
		$damageVal = (int) $data->getWandDamage();
		$params[":name"] = $name;
		$params[":wandidtype"] = $idType;
		$params[":wandidval"] = $idVal;
		$params[":wanddamagetype"] = $damageType;
		$params[":wanddamageval"] = $damageVal;
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
