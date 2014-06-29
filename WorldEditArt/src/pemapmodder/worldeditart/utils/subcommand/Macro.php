<?php

namespace pemapmodder\worldeditart\utils\subcommand;

use pocketmine\level\Position;
use pocketmine\Player;

class Macro extends Subcommand{
	public function getName(){
		return "macro";
	}
	public function getDescription(){
		return "Manage WorldEditArt macros";
	}
	public function getUsage(){
		return "<start|end|pause|resume|run|help>"; // TODO
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
				return "";
			case "end":
				return "";
			case "pause":
				return "";
			case "resume":
				return "";
			case "run":
				if(!$player->hasPermission("wea.macro.run")){
					return self::NO_PERM;
				}
				return "";
			default:
				return str_replace("\r\n", "\n", <<<EOH
§d/wea macro start §a §bStart recording a macro
§d/wea macro end §a<name|NG> §bStop recording a macro and save it, or discard it if NG is given
§d/wea macro pause §a §bPause recording a macro
§d/wea macro resume §a §bResume recording a macro
§d/wea macro run §a<name> §bRun a macro
EOH
				);
		}
	}
}
