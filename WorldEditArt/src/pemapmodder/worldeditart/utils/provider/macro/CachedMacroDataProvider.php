<?php

namespace pemapmodder\worldeditart\utils\provider\macro;

use pemapmodder\worldeditart\WorldEditArt;
use pemapmodder\worldeditart\tasks\GarbageCollectionTask;
use pemapmodder\worldeditart\utils\macro\Macro;
use pemapmodder\worldeditart\utils\provider\Cached;

abstract class CachedMacroDataProvider extends MacroDataProvider implements Cached{
	/** @var Macro[] */
	private $caches = [];
	public function __construct(WorldEditArt $main){
		parent::__construct($main);
		$this->initGC();
	}
	protected function initGC(){
		$this->getMain()->getServer()->getScheduler()->scheduleDelayedRepeatingTask(
			new GarbageCollectionTask($this->getMain(), $this), 1200, 200);
	}
	public function offsetExists($name){
		$name = strtolower($name);
		if(isset($this->caches[$name])){
			return true;
		}
		$macro = $this->readMacro($name);
		if($macro instanceof Macro){
			$this->caches[$name] = $macro;
			return true;
		}
		return false;
	}
	public function offsetGet($name){
		$name = strtolower($name);
		if($this->offsetExists($name)){
			return $this->caches[$name];
		}
		return null;
	}
	public function offsetSet($name, $value){
		$name = strtolower($name);
		if(!($value instanceof Macro)){
			throw new \InvalidArgumentException("Trying to set macro data provider value to non-macro");
		}
		if(!$value->isAppendable()){
			throw new \BadMethodCallException("Trying to save non-appendable macro '$name' into macro data provider");
		}
		$this->caches[$name] = $value;
		$this->saveMacro($name, $value);
	}
	public function offsetUnset($name){
		$name = strtolower($name);
		if(isset($this->caches[$name])){
			unset($this->caches[$name]);
		}
		$this->deleteMacro($name);
	}
	/**
	 * @param $name
	 * @return Macro|null
	 */
	public abstract function readMacro($name);
	/**
	 * @param $name
	 * @param Macro $macro
	 */
	public abstract function saveMacro($name, Macro $macro);
	/**
	 * @param $name
	 */
	public abstract function deleteMacro($name);
	public function collectGarbage($expiryTime){
		foreach($this->caches as $name => $macro){
			if(microtime(true) - $macro->getCreationTime() > $expiryTime){
				unset($this->caches[$name]);
			}
		}
	}
}
