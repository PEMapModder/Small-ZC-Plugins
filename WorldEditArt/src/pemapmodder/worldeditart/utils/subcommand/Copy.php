<?php

namespace pemapmodder\worldeditart\utils\subcommand;

use pemapmodder\worldeditart\utils\clip\Clip;
use pemapmodder\worldeditart\utils\spaces\Space;
use pocketmine\Player;

class Copy extends Subcommand{
	public function getName(){
		return "copy";
	}

	public function getDescription(){
		return "Copy your selection";
	}

	public function getUsage(){
		return "[-a|-anchor] [name = default] [g|global]";
	}

	public function checkPermission(
		/** @noinspection PhpUnusedParameterInspection */
		Space $space, Player $player){
		return true; // TODO
	}

	public function onRun(array $args, Space $space, Player $player){
		$anchor = $player->getPosition();
		if(isset($args[0]) and ($args[0] === "-a" or $args[0] === "-anchor")){
			$anchor = $this->getMain()->getAnchor($player);
		}
		if(!isset($args[0])){
			array_unshift($args, "default");
		}
		$clip = new Clip($space, $anchor, array_shift($args));
		if(isset($args[0]) and ($args[0] === "g" or $args[0] === "global")){
			$clipboard = $this->getMain()->getClipboardProvider();
			$clipboard[$clip->getName()] = $clip;
		}else{
			$this->getMain()->setClip($player, $clip, $clip->getName());
		}
		return "Clip " . $clip->getName() . " copied.";
	}
}
