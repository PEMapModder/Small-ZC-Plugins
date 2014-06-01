<?php

namespace pemapmodder\invgui\examples;

use pemapmodder\invgui\Main as Registerer;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\TextFormat as Font;

class Main extends PluginBase{
	public function onEnable(){
		foreach(array(new ExampleChildGUI(), new ExampleGUI(), new ExampleParentGUI()) as $gui)
			Registerer::register($gui);
		console(Font::
	}
}
