<?php

namespace pemapmodder\worldeditart\utils\provider\player;

abstract class SQLPlayerDataProvider extends PlayerDataProvider{
	const TRUE = 1;
	const FALSE = 0;
	const INT = 2;
	/**
	 * @param string $name
	 */
	protected abstract function deletePlayerName($name);
	protected abstract function insertTool($name, $id, $it, $iv, $dt, $dv);
	protected abstract function fetchPlayer($name);
	/**
	 * @param string $name
	 * @return array[]
	 */
	public function offsetGet($name){
		$arrays = $this->fetchPlayer($name = strtolower($name));
		$wand = null;
		$jump = null;
		foreach($arrays as $arr){
			$name = $arr["player"];
			$tool = $arr["tool_id"];
			$typedID = [$arr["item_id_type"], $arr["item_id_value"]];
			$typedDamage = [$arr["item_id_type"], $arr["item_id_value"]];
			$id = self::typedToMixed($typedID);
			$damage = self::typedToMixed($typedDamage);
			$c = $this->getMain()->getConfig();
			switch($tool){
				case PlayerData::WAND:
					$wand = new SelectedTool($id, $damage, $c->get("wand-id"), $c->get("wand-damage"));
					break;
				case PlayerData::JUMP:
					$jump = new SelectedTool($id, $damage, $c->get("jump-id"), $c->get("jump-damage"));
					break;
			}
		}
		return new PlayerData($this->getMain(), $name, $wand, $jump);
	}
	public function offsetSet($name, $data){
		if(!($data instanceof PlayerData)){
			throw new \InvalidArgumentException("Trying to set a player data provider element to non-PlayerData");
		}
		$name = strtolower($name);
		$this->insert($name, PlayerData::WAND, $data->getWand());
		$this->insert($name, PlayerData::JUMP, $data->getJump());
	}
	private function insert($name, $id, SelectedTool $tool){
		$typed = self::mixedToTyped($tool->getRawID());
		$it = $typed[0];
		$iv = $typed[1];
		$typed = self::mixedToTyped($tool->getRawDamage());
		$dt = $typed[0];
		$dv = $typed[1];
		$this->insertTool($name, $id, $it, $iv, $dt, $dv);
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
	public static function mixedToTyped($mixed){
		return [
			is_bool($mixed) ? ($mixed ? self::TRUE:self::FALSE):self::INT,
			(int) $mixed
		];
	}
	public static function typedToMixed(array $typed){
		return $typed[0] === self::INT ? $typed[1]:($typed[0] === self::TRUE);
	}
}
