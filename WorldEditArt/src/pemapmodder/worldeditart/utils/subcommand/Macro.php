<?php

namespace pemapmodder\worldeditart\utils\subcommand;

use pemapmodder\worldeditart\utils\macro\ExecutableMacro;
use pemapmodder\worldeditart\utils\macro\RecordingMacro;
use pocketmine\level\Position;
use pocketmine\Player;

class Macro extends Subcommand{
	const NOT_RECORDING = "You are not recording a macro!";
	public function getName(){
		return "macro";
	}
	public function getDescription(){
		return "Manage WorldEditArt macros";
	}
	public function getUsage(){
		return "<start|end|pause|resume|run|help>";
	}
	public function checkPermission(Player $player){
		if(!($this->getMain()->getAnchor($player) instanceof Position)){
			return false;
		}
		return $player->hasPermission("wea.macro.record") or $player->hasPermission("wea.macro.run");
	}
	public function onRun(array $args, Player $player){
		switch($cmd = strtolower(array_shift($args))){
			case "start":
				if(!$player->hasPermission("wea.macro.record")){
					return self::NO_PERM;
				}
				$anchor = $this->getMain()->getAnchor($player);
				$macro = new RecordingMacro($player, $anchor);
				$this->getMain()->setRecordingMacro($player, $macro);
				return "You are now recording a macro.";
			case "end":
				$macro = $this->getMain()->getRecordingMacro($player);
				if(!($macro instanceof RecordingMacro)){
					return self::NOT_RECORDING;
				}
				if(!isset($args[0])){
					return self::WRONG_USE;
				}
				$name = array_shift($args);
				if(strtoupper($name) === "NG"){
					$this->getMain()->setRecordingMacro($player, null);
					return "Your macro has been discarded.";
				}
				if(file_exists($this->getMain()->getDataFolder()."macros/$name.mcr")){
					return "Macro $name already exists!";
				}
				$macro->saveTo(fopen($this->getMain()->getDataFolder()."macros/$name.mcr", "rb"));
				return "Macro $name has been saved.";
			case "pause":
				$macro = $this->getMain()->getRecordingMacro($player);
				if(!($macro instanceof RecordingMacro)){
					return self::NOT_RECORDING;
				}
				$macro->setHibernating(true);
				return "Macro recording successfully paused.";
			case "resume":
				$macro = $this->getMain()->getRecordingMacro($player);
				if(!($macro instanceof RecordingMacro)){
					return self::NOT_RECORDING;
				}
				$macro->setHibernating(false);
				return "Macro recording has been continued.";
			case "run":
				if(!$player->hasPermission("wea.macro.run")){
					return self::NO_PERM;
				}
				if(!isset($args[0])){
					return self::NO_PERM;
				}
				$name = array_shift($args);
				if(!is_file($path = $this->getMain()->getDataFolder()."macros/$name.mcr")){
					return "Macro $name not found! Maybe it has been deleted?";
				}
				$macro = new ExecutableMacro(file_get_contents($path));
				$this->getMain()->getLogger()->info("Running macro $name by ".$macro->getAuthor()."...");
				return "";
			default:
				return trim(str_replace("\r", "\n", str_replace("\r\n", "\n", <<<EOH
§d/wea macro start §a §bStart recording a macro
§d/wea macro end §a<name|NG> §bStop recording a macro and save it, or discard it if NG is given
§d/wea macro pause §a §bPause recording a macro
§d/wea macro resume §a §bResume recording a macro
§d/wea macro run §a<name> §bRun a macro
EOH
				)));
		}
	}
}
