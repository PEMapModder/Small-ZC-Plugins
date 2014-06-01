<?php

namespace pemapmodder\invgui\examples;

use pemapmodder\invgui\InvGui;
use pocketmine\Player;

class ExampleParentGUI extends InvGui{
	public function getID(){
		return InvGui::calcId(WOOL, 5);
	}
	public function getInheritance(){
		return array();
	}
	public function onClicked(Player $player){
		$player->sendChat("You clicked ExampleParentGUI!");
		$player->sendChat("Opening child GUI list!");
		return true;
	}
	public function isParent(){
		return true;
	}
}
