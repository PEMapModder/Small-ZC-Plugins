<?php

namespace pemapmodder\worldeditart\utils\subcommand;

use pemapmodder\worldeditart\utils\spaces\Space;
use pocketmine\Player;

class Desel extends Subcommand{
	public function getName(){
		return "desel";
	}
	public function getDescription(){
		return "Remove yoru selection";
	}
	public function getUsage(){
		return "";
	}

	public function onRun(
		/** @noinspection PhpUnusedParameterInspection */ array $args,
		/** @noinspection PhpUnusedParameterInspection */ Space $space, Player $player){
		$this->getMain()->unsetSelection($player);
	}
	public function checkPermission(
		/** @noinspection PhpUnusedParameterInspection */ Space $space, Player $player){
		return $player->hasPermission("wea.desel");
	}
}
