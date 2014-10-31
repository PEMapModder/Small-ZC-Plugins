<?php

namespace pemapmodder\worldeditart\utils\subcommand;

use pemapmodder\worldeditart\utils\macro\Macro;
use pocketmine\level\Position;
use pocketmine\Player;

class MacroSubcmd extends Subcommand{
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
		return $player->hasPermission("wea.macro.*");
	}
	public function onRun(array $args, Player $player){
		if(!isset($args[0])){
			return self::WRONG_USE;
		}
		switch($sub = strtolower(array_shift($args))){
			case "start":
				if(!$player->hasPermission("wea.macro.start")){
					return self::NO_PERM;
				}
				if($this->getMain()->getRecordingMacro($player) instanceof Macro){
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
				$macro = new Macro(true, $anchor, $player->getName());
				$this->getMain()->setRecordingMacro($player, $macro);
				return "You are now recording a macro.";
			case "save":
				if(!isset($args[0])){
					return "Usage: /w macro save <name>";
				}
				if(!$player->hasPermission("wea.macro.save")){
					return self::NO_PERM;
				}
				break;
			case "wait":
				if(!$player->hasPermission("wea.macro.resume")){
					return self::NO_PERM;
				}
				break;
			case "ng":
				if(!$player->hasPermission("wea.macro.ng")){
					return self::NO_PERM;
				}
				break;
			case "pause":
				if(!$player->hasPermission("wea.macro.pause")){
					return self::NO_PERM;
				}
				break;
			case "resume":
				if(!$player->hasPermission("wea.macro.resume")){
					return self::NO_PERM;
				}
				break;
			default:
				return self::WRONG_USE;
		}
		$macro = $this->getMain()->getRecordingMacro($player);
		if(!($macro instanceof Macro)){
			return "You don't have a recording macro!";
		}
		switch($sub){
			case "ng":
				$this->getMain()->unsetRecordingMacro($player);
				return "Your recording macro has been terminated.";
			case "save":
				$name = array_shift($args);
				$macroProvider = $this->getMain()->getMacroDataProvider();
				if(isset($macroProvider[$name])){
					return "Such macro already exists!";
				}
				try{
					$macroProvider[$name] = $macro;
					return "Macro $name has been saved.";
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
