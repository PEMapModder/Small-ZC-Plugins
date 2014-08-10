<?php

namespace pemapmodder\worldeditart\utils\provider\clip;

use pemapmodder\worldeditart\Main;
use pemapmodder\worldeditart\tasks\GarbageCollectionTask;
use pemapmodder\worldeditart\utils\clip\Clip;
use pemapmodder\worldeditart\utils\provider\Cached;

abstract class CachedClipboardProvider extends ClipboardProvider implements Cached{
	/** @var Clip[] */
	private $caches = [];
	public function __construct(Main $main){
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
		$clip = $this->getClip($name);
		if($clip instanceof Clip){
			$this->caches[$name] = $clip;
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
		if(!($value instanceof Clip)){
			throw new \InvalidArgumentException("Trying to set clipboard value to non-clip");
		}
		$this->caches[$name] = $value;
		$this->setClip($name, $value);
	}
	public function offsetUnset($name){
		$name = strtolower($name);
		if(isset($this->caches[$name])){
			unset($this->caches[$name]);
		}
		$this->deleteClip($name);
	}
	/**
	 * @param $name
	 * @return Clip|null
	 */
	public abstract function getClip($name);
	/**
	 * @param $name
	 * @param Clip $clip
	 */
	public abstract function setClip($name, Clip $clip);
	/**
	 * @param $name
	 */
	public abstract function deleteClip($name);
	public function collectGarbage($expiryTime){
		foreach($this->caches as $name => $clip){
			if(microtime(true) - $clip->getCreationTime() > $expiryTime){
				unset($this->caches[$name]);
			}
		}
	}
}
