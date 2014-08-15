<?php

namespace pemapmodder\worldeditart\utils\provider\player;

use pemapmodder\worldeditart\Main;

class PlayerData{
	const USE_DEFAULT = true;
	const ALLOW_ANY = false;
	const WAND = 1;
	const JUMP = 2;
	/** @var Main */
	private $main;
	/** @var string */
	private $name;
	/** @var SelectedTool[] */
	private $tools = [];
	public function __construct(Main $main, $name, SelectedTool $wand = null, SelectedTool $jump = null){
		$config = $main->getConfig();
		if($wand === null){
			$wand = new SelectedTool($main, PlayerData::USE_DEFAULT, PlayerData::USE_DEFAULT, $config->get("wand-id"), $config->get("wand-damage"));
		}
		if($jump === null){
			$jump = new SelectedTool($main, PlayerData::USE_DEFAULT, PlayerData::USE_DEFAULT, $config->get("jump-id"), $config->get("jump-damage"));
		}
		$this->main = $main;
		$this->name = $name;
		$this->tools = [
			self::WAND => $wand,
			self::JUMP => $jump
		];
	}
	public function update(){
		$this->main->getPlayerDataProvider()[$this->name] = $this;
	}
	public function __toString(){
		return $this->name;
	}
	/**
	 * @return SelectedTool
	 */
	public function getWand(){
		return $this->tools[self::WAND];
	}
	/**
	 * @param SelectedTool $wand
	 */
	public function setWand($wand){
		$this->tools[self::WAND] = $wand;
		$this->update();
	}
	/**
	 * @return SelectedTool
	 */
	public function getJump(){
		return $this->tools[self::JUMP];
	}
	/**
	 * @param $jump
	 */
	public function setJump($jump){
		$this->tools[self::JUMP] = $jump;
		$this->update();
	}
	public function getTool($id){
		return isset($this->tools[$id]) ? $this->tools[$id]:null;
	}
	public function setTool($id, SelectedTool $tool){
		$this->tools[$id] = $tool;
		$this->update();
	}
}
