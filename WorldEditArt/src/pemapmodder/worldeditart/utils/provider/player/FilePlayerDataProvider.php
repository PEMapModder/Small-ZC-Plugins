<?php

namespace pemapmodder\worldeditart\utils\provider\player;

use pemapmodder\worldeditart\WorldEditArt;

abstract class FilePlayerDataProvider extends PlayerDataProvider{
	private $path;

	public function __construct(WorldEditArt $main, $path){
		parent::__construct($main);
		$this->path = $path;
	}

	public function getPath($name){
		$file = str_replace("<name>", strtolower($name), $this->path);
		@mkdir(dirname($file), 0777, true);
		return $file;
	}

	public function isAvailable(){
		return true;
	}

	public function close(){
	}

	public function offsetGet($name){
		if(is_file($this->getPath($name))){
			$data = $this->parseFile($this->getPath($name));
			$config = $this->getMain()->getConfig();
			$wand = new SelectedTool($data["wand-id"], $data["wand-damage"], $config->get("wand-id"), $config->get("wand-damage"));
			$jump = new SelectedTool($data["jump-id"], $data["jump-damage"], $config->get("jump-id"), $config->get("jump-damage"));
			return new PlayerData($name, $name, $wand, $jump);
		}
		return new PlayerData($this->getMain(), $name);
	}

	public function offsetSet($name, $data){
		if(!($data instanceof PlayerData)){
			throw new \InvalidArgumentException("Player data passed to FilePlayerDataProvider must be instance of PlayerData, " .
				(is_object($data) ? get_class($data) : gettype($data)) . " given");
		}
		$this->emitFile($this->getPath($name), [
			"wand-id" => $data->getWand()->getRawID(),
			"wand-damage" => $data->getWand()->getRawDamage(),
			"jump-id" => $data->getJump()->getRawID(),
			"jump-damage" => $data->getJump()->getRawDamage(),
		]);
	}

	public function offsetUnset($name){
		@unlink($this->getPath($name));
	}

	protected abstract function parseFile($file);

	protected abstract function emitFile($file, $data);
}
