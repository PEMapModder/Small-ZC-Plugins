<?php

namespace pemapmodder\worldeditart\utils\subcommand;

use pemapmodder\worldeditart\utils\macro\RecordingMacro;
use pocketmine\level\Position;
use pocketmine\Player;

class Macro extends Subcommand{
	public function getName(){
		return "macro";
	}
	public function getDescription(){
		return "Manage macros";
	}
	public function getUsage(){
		return "/w macro <start [a|anchor]|ng|save <name>|wait|pause|resume>";
	}
	public function checkPermission(Player $player){
		// TODO
	}
	public function onRun(array $args, Player $player){
		if(!isset($args[0])){
			return self::WRONG_USE;
		}
		switch($sub = strtolower(array_shift($args))){
			case "start":
				if($this->getMain()->getRecordingMacro($player) instanceof RecordingMacro){
					return "You are already recording a macro!";
				}
				$anchor = $player->getPosition();
				while(isset($args[0])){
					$arg = array_shift($args);
					switch($arg){
						case "a":
						case "anchor":
							$anchor = $this->getMain()->getAnchor($player);
							if(!($anchor instanceof Position)){
								return self::NO_ANCHOR;
							}
					}
				}
				$macro = new RecordingMacro($player, $anchor);
				$this->getMain()->setRecordingMacro($player, $macro);
				return "You are now recording a macro.";
			case "save":
				if(!isset($args[0])){
					return "Usage: /w macro save <name>";
				}
			case "ng":
			case "wait":
			case "pause":
			case "resume":
				$macro = $this->getMain()->getRecordingMacro($player);
				if(!($macro instanceof RecordingMacro)){
					return "You don't have a recording macro!";
				}
				break;
			default:
				return self::WRONG_USE;
		}
		switch($sub){
			case "ng":
				$this->getMain()->unsetRecordingMacro($player);
				return "Your recording macro has been terminated.";
			case "save":
				$name = array_shift($args);
				$file = $this->getMain()->getMacroFile($name);
				if(file_exists($file)){
					return "Such macro already exists!";
				}
				$stream = gzopen($file, "wb");
				if(!is_resource($stream)){
					return "Unable to open stream. Maybe \"".$file."\" is an invalid filename?";
				}
				$macro->saveTo($stream);
				return "Macro \"".$name."\" saved.";
			case "wait":

		}
	}
}
