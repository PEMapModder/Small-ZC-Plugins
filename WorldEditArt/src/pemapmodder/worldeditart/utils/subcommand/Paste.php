<?php

namespace pemapmodder\worldeditart\utils\subcommand;

use pemapmodder\worldeditart\utils\clip\Clip;
use pemapmodder\worldeditart\utils\provider\clip\ClipboardProvider;
use pocketmine\Player;

class Paste extends Subcommand{
	public function getName(){
		return "paste";
	}

	public function getDescription(){
		return "Paste your current clip or a global clip";
	}

	public function getUsage(){
		return "[clip name = default] [g] [a|anchor]";
	}

	public function checkPermission(Player $player){
		return $player->hasPermission("wea.paste");
	}

	public function onRun(array $args, Player $player){
		$global = false;
		$anchor = $player->getPosition();
		$name = isset($args[0]) ? array_shift($args) : "default";
		while(isset($args[0])){
			$arg = array_shift($args);
			switch($arg){
				case "g":
					$global = $this->getMain()->getClipboardProvider();
					break;
				case "a":
				case "anchor":
					$anchor = $this->getMain()->getAnchor($player);
					break;
			}
		}
		if($global instanceof ClipboardProvider){
			$clip = $global[$name];
		}else{
			$clip = $this->getMain()->getClip($player, $name);
		}
		if(!($clip instanceof Clip)){
			if($global instanceof ClipboardProvider){
				return "There isn't a global clip called $name!";
			}else{
				return "You don't have a clip called $name!";
			}
		}
		$clip->paste($anchor);
		return count($clip->getBlocks()) . " block(s) have been pasted.";
	}
}
