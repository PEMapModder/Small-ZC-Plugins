<?php

namespace pemapmodder\worldeditart\utils\subcommand;

use pemapmodder\worldeditart\utils\macro\Macro as MacroObj;
use pemapmodder\worldeditart\utils\provider\macro\MacroDataProvider;
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
		return "<start [a|anchor]|ng|save <name>|wait <ticks>|pause|resume>";
	}
	public function checkPermission(Player $player){
		return true; // TODO
	}
	public function onRun(array $args, Player $player){
		if(!isset($args[0])){
			return self::WRONG_USE;
		}
		switch($sub = strtolower(array_shift($args))){
			case "start":
				if($this->getMain()->getRecordingMacro($player) instanceof MacroObj){
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
				$macro = new MacroObj(false, $anchor, $player);
				$this->getMain()->setRecordingMacro($player, $macro);
				return "You are now recording a macro.";
			case "run":
				if(!isset($args[0])){
					return self::WRONG_USE;
				}
				$name = array_shift($args);
				$tokens = explode(":", $name);
				if(isset($tokens[2])){
					return "Macros don't have colons (:) in their names.";
				}
				if(!isset($tokens[1])){
					array_unshift($tokens, "default");
				}
				$database = $tokens[0];
				$name = $tokens[1];
				if($this->getMain()->getRecordingMacro($player) instanceof MacroObj){
					return "For the sake of safety, you cannot run a macro during macro recording.";
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
							break;
					}
				}
				$provider = $this->getMain()->getMacroDataProvider($database);
				if(!($provider instanceof MacroDataProvider)){
					return "Macro provider $database doesn't exist! Macro cannot have colons (:) in their names.";
				}
				$macro = $provider[$name];
				if(!($macro instanceof MacroObj)){
					return "Macro $name doesn't exist.";
				}
				$macro->execute($this->getMain()->getServer()->getScheduler(), $anchor, $this->getMain());
				return "The macro is now running.";
			case "save":
			case "wait":
				if(!isset($args[0])){
					return "Usage: /w macro save <name>";
				}
			case "ng":
			case "pause":
			case "resume":
				$macro = $this->getMain()->getRecordingMacro($player);
				if(!($macro instanceof MacroObj)){
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
				$tokens = explode(":", $name);
				if(isset($tokens[2])){
					return "Macro name cannot contain a colon (:)!";
				}
				if(!isset($tokens[1])){
					array_unshift($tokens, "default");
				}
				$macroProvider = $this->getMain()->getMacroDataProvider($tokens[0]);
				if(!($macroProvider instanceof MacroDataProvider)){
					return "Macro provider $tokens[0] doesn't exist! Macros cannot have colons (:) in their names.";
				}
				if(isset($macroProvider[$name])){
					return "Such macro already exists!";
				}
				try{
					$macroProvider[$name] = $macro;
				}
				catch(\Exception $e){
					return "An error occurred. Type: ".(new \ReflectionClass($e))->getShortName()."; Message: ".$e->getMessage();
				}
			case "wait":
				$ticks = (int) round(floatval(array_shift($args)) * 20);
				$macro->wait($ticks);
				return "A $ticks-tick waiting added.";
			case "pause":
				if($macro->isHibernating()){
					return "Your macro is already paused. Use \"/w macro resume\" to resume.";
				}
				$macro->setHibernating(true);
				return "Your macro has been paused.";
			case "resume":
				if(!$macro->isHibernating()){
					return "Your macro is running! Use \"/w macro pause\" to pause.";
				}
				$macro->setHibernating(true);
				return "Your macro has been resumed.";
		}
		return self::WRONG_USE;
	}
}
