<?php

namespace pemapmodder\invgui\examples;

use pemapmodder\invgui\InvGui;
use pocketmine\Player;

class ExampleGUI extends InvGui{
	public function getID(){
		return InvGui::calcId(WOOL, 3);
	}
	public function getInheritance(){
		return array();
	}
	public function onClicked(Player $player){
		$player->sendChat("You clicked ExampleGUI!");
		return true;
	}
	public function isParent(){
		return false;
	}
}
