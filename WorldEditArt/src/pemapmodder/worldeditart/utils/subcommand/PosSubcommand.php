<?php

namespace pemapmodder\worldeditart\utils\subcommand;

use pemapmodder\worldeditart\Main;
use pemapmodder\worldeditart\utils\spaces\CuboidSpace;
use pemapmodder\worldeditart\utils\spaces\Space;
use pocketmine\level\Position;
use pocketmine\Player;

class PosSubcommand extends Subcommand{
	/** @var bool */
	protected $is2;
	/**
	 * @param Main $main
	 * @param bool $is2
	 */
	public function __construct(Main $main, $is2){
		parent::__construct($main);
		$this->is2 = $is2;
	}
	public function getName(){
		return "pos".($this->is2 ? "2":"1");
	}
	public function getDescription(){
		return "Set your position ".($this->is2 ? "2":"1");
	}
	public function getUsage(){
		return "[me|here|a|anchor|c|crosshair]";
	}
	public function getAliases(){
		if($this->is2){
			return ["p2", "2"];
		}
		else{
			return ["p1", "1"];
		}
	}
	public function checkPermission(Player $player){
		// TODO
	}
	public function onRun(array $args, Player $player){
		$flag = 0; // 0 for me, 1 for anchor, 2 for crosshair
		if(isset($args[0])){
			$arg = array_shift($args);
			switch($arg){
				case "a":
				case "anchor":
					$flag = 1;
					break;
				case "c":
				case "crosshair":
					$flag = 2;
					break;
			}
		}
		$selected = $player->getPosition();
		if($flag === 1){
			$selected = $this->getMain()->getAnchor($player);
			if(!($selected instanceof Position)){
				return self::NO_ANCHOR;
			}
		}
		if($flag === 2){
			$selected = Main::getCrosshairTarget($player);
			if(!($selected instanceof Position)){
				return "The block is too far/in the void/in the sky.";
			}
		}
		if($selected->y < 0 or $selected->y > (defined($path = "pemapmodder\\worldeditart\\MAX_WORLD_HEIGHT") ? constant($path):127)){
			return "The selected point is too high/too low.";
		}
		$selected->getLevel()->loadChunk($selected->x >> 4, $selected->z >> 4);
		$selected = Position::fromObject($selected->floor(), $selected->getLevel());
		$space = $this->getMain()->getSelection($player);
		if($space instanceof CuboidSpace and $space->getLevel() === $selected->getLevel()){
			if($this->is2){
				$space->set1($selected);
			}
			else{
				$space->set0($selected);
			}
			goto end;
		}
		$temp = $this->getMain()->getTempPos($player);
		if(is_array($temp) and $temp["#"] !== $this->is2){
			/** @var Position $pos */
			$pos = $temp["position"];
			if($pos->getLevel() === $selected->getLevel()){
				if($this->is2){
					$this->getMain()->setSelection($player, new CuboidSpace($pos, $selected));
				}
				else{
					$this->getMain()->setSelection($player, new CuboidSpace($selected, $pos));
				}
				goto end;
			}
		}
		$this->getMain()->unsetSelection($player);
		$this->getMain()->setTempPos($player, $selected, $this->is2);
		end:
		$space = $this->getMain()->getSelection($player);
		if($space instanceof Space){
			$cnt = count($space->getPosList());
		}
		return ($this->is2 ? "Second":"First")." position set to ({$selected->x}, {$selected->y}, {$selected->z}).".(isset($cnt) ? "\n  (Cuboid position with $cnt blocks set)":"");
	}
}
