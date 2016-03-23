<?php

namespace customareas\db;

use customareas\area\Area;
use customareas\CustomAreas;
use pocketmine\nbt\NBT;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\EnumTag;
use pocketmine\nbt\tag\IntTag;
use pocketmine\nbt\tag\StringTag;

class LocalDatabase extends Database{
	private $indexFile;
	private $areaFile;
	/** @var Area[] */
	private $areas = [];
	/** @var CustomAreas */
	private $main;

	public function __construct(CustomAreas $main){
		$this->main = $main;
	}

	public function getName(){
		return "local";
	}

	public function init($args){
		$this->indexFile = $args["index-file"];
		$this->areaFile = $args["area-file"];
		$dir = dirname($this->indexFile);
		if(!is_dir($dir)){
			mkdir($dir, 0777, true);
		}elseif(is_file($this->indexFile)){
			$c = file_get_contents($this->indexFile);
			foreach(explode("\0", $c) as $name){
				try{
					$this->areas[$name] = $this->loadArea($name);
				}catch(\RuntimeException $e){
					$this->main->getLogger()->error("Failed to load area of name '$name': {$e->getMessage()}");
				}
			}
		}
	}

	public function close(){
		$handle = fopen($this->indexFile, "wb");
		foreach($this->areas as $area){
			$this->saveArea($area);
			fwrite($handle, strtolower($area->getName()));
			fwrite($handle, "\0");
		}
		fclose($handle);
	}

	public function loadArea($name){
		$file = str_replace('$${areaname}', strtolower($name), $this->areaFile);
		if(!is_file($file)){
			throw new \RuntimeException("Area not found");
		}
		$nbt = new NBT(NBT::BIG_ENDIAN);
		$nbt->readCompressed(file_get_contents($file));
		$data = $nbt->getData();
		$name = $data->CaseName->getValue();
		$shape = unserialize($data->SerializedShape->getValue());
		$userFlags = $data->UserFlags->getValue();
		$nonUserFlags = $data->NonUserFlags->getValue();
		$owner = $data->Owner->getValue();
		$users = array_map(function (StringTag $tag){
			return $tag->getValue();
		}, $data->Users->getValue());
		return new Area($name, $shape, $userFlags, $nonUserFlags, $owner, $users);
	}

	public function saveArea(Area $area){
		$nbt = new NBT(NBT::BIG_ENDIAN);
		$data = new CompoundTag();
		$data->CaseName = new StringTag("CaseName", $area->getName());
		$data->SerializedShape = new StringTag("SerializedShape", serialize($area->getShape()));
		$data->UserFlags = new IntTag("UserFlags", $area->getUserFlags());
		$data->NonUserFlags = new IntTag("NonUserFlags", $area->getNonUserFlags());
		$data->Owner = new StringTag("Owner", $area->getOwner());
		$data->Users = new EnumTag("Users", array_map(function ($user){
			return new StringTag("", $user);
		}, $area->getUsers()));
		$file = str_replace('$${areaname}', strtolower($area->getName()), $this->areaFile);
		file_put_contents($file, $nbt->writeCompressed());
	}

	public function addArea(Area $area){
		$this->areas[$area->getName()] = $area;
	}

	public function rmArea(Area $area){
		unset($this->areas[$area->getName()]);
	}

	public function getArea($name){
		return isset($this->areas[$name]) ? $this->areas[$name] : null;
	}

	public function getAreas(){
		return $this->areas;
	}
}
