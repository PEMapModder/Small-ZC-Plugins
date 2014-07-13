<?php

namespace pemapmodder\worldeditart\utils\subcommand;

use pemapmodder\worldeditart\events\PasteEvent;
use pemapmodder\worldeditart\Main;
use pemapmodder\worldeditart\utils\spaces\CylinderSpace;
use pocketmine\level\Position;
use pocketmine\Player;

class Paste extends Subcommand{
	public function getName(){
		return "paste";
	}
	public function getDescription(){
		return "Paste your selection to the world";
	}
	public function getUsage(){
		return "[-a] [-r] [-g <name>]";
	}
	public function checkPermission(Player $player){
		return $player->hasPermission("wea.clipboard.paste");
	}
	public function onRun(array $args, Player $player){
		$clip = $this->getMain()->getClip($player);
		$global = array_search("-g", $args);
		if($global !== false){
			$name = array_slice($args, $global + 1);
			$clip = $this->getMain()->getGlobalClip(implode(" ", $name));
			if(!is_array($clip)){
				return "Global clip \"$name\" not found.";
			}
			$args = array_slice($args, 0, $global);
		}
		if(!is_array($clip)){
			return "Your clipboard is empty!";
		}
		$ref = Position::fromObject($player->getPosition()->floor(), $player->getLevel());
		if(in_array("-a", $args)){
			$ref = $this->getMain()->getAnchor($player);
			if(!($ref instanceof Position)){
				return "You don't have an anchor set.";
			}
		}
		if(in_array("-r", $args)){
			$copyVector = CylinderSpace::getVector($clip["yaw"], true);
			$curVector = CylinderSpace::getVector($player->yaw, true);
			if($copyVector !== $curVector){
				$clip["blocks"] = Main::rotateBlocks($clip["blocks"], $copyVector, $curVector, $ref);
			}
		}
		$cnt = 0;
		$this->getMain()->getServer()->getPluginManager()->callEvent(new PasteEvent($player, $clip, $global !== false));
		$player->sendMessage("Copying a clip of ".(count($clip["blocks"]))." block(s) authored by ".$clip["author"]." to ".Main::posToStr($ref)."..");
		foreach($clip["blocks"] as $block){
			if($ref->getLevel()->setBlock($block->add($ref), $block, true, false) !== false){
				$cnt++;
			}
		}
		return "$cnt block(s) have been pasted";
	}
}
