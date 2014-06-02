<?php

namespace WorldGenPro;

use pocketmine\event\Listener;
use pocketmine\plugin\PluginBase;
use pocketmine\level\generator\Generator;

class Loader extends PluginBase implements Listener{
	public function onEnable(){
		$path = __NAMESPACE__ . "\\Generators\\";
		Generator::addGenerator($path . "TheEnd", "TheEnd");
		Generator::addGenerator($path . "Nether", "Nether");
		Generator::addGenerator($path . "SuperflatBiome", "SuperflatBiome");
	}
}
