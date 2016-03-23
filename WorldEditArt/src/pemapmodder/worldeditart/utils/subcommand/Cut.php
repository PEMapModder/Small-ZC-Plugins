<?php

namespace pemapmodder\worldeditart\utils\subcommand;

use pemapmodder\worldeditart\utils\spaces\Space;
use pocketmine\Player;

class Cut extends Copy{
	public function getName(){
		return "cut";
	}

	public function getDescription(){
		return "Copy your selection and delete the blocks inside it";
	}

	public function checkPermission(Space $space, Player $player){
		return true; // TODO
	}

	public function onRun(array $args, Space $space, Player $player){
		$result = parent::onRun($args, $space, $player);
		$space->clear();
		return $result;
	}
}
