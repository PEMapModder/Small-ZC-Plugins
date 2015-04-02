<?php

namespace customareas\pocketmine;

use customareas\shape\Cached;
use pocketmine\scheduler\PluginTask;

class CacheCleanTask extends PluginTask{
	public function onRun($t){
		/** @var \customareas\CustomAreas $main */
		$main = $this->getOwner();
		foreach($main->getDatabase()->getAreas() as $area){
			$shape = $area->getShape();
			if($shape instanceof Cached){
				$shape->cleanCache();
			}
		}
	}
}
