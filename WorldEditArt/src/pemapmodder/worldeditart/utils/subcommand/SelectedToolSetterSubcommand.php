<?php

namespace pemapmodder\worldeditart\utils\subcommand;

use pemapmodder\worldeditart\Main;
use pemapmodder\worldeditart\utils\provider\player\PlayerData;
use pemapmodder\worldeditart\utils\provider\player\SelectedTool;
use pocketmine\block\Air;
use pocketmine\Player;

class SelectedToolSetterSubcommand extends Subcommand{
	/** @var string */
	private $name;
	/** @var int */
	private $id;
	/** @var string */
	private $defaultPrefix;
	/**
	 * @param Main $main
	 * @param string $name
	 * @param int $id
	 * @param string $defaultPrefix
	 */
	public function __construct(Main $main, $name, $id, $defaultPrefix){
		parent::__construct($main);
		$this->name = $name;
		$this->id = $id;
		$this->defaultPrefix = $defaultPrefix;
	}
	public function getName(){
		return $this->name;
	}
	public function getUsage(){
		return "[cd|check-damage|v|view|rm|remove|del|delete]";
	}
	public function getDescription(){
		return "Set/view own's {$this->name} tool";
	}
	public function checkPermission(Player $player){
		return $player->hasPermission("wea.tool.{$this->getName()}");
	}
	public function onRun(array $args, Player $player){
		$cd = false;
		$mode = 0; // 0 for set hand, 1 for view, 2 for removal
		while(isset($args[0])){
			$arg = array_shift($args);
			switch($arg){
				case "cd":
				case "check-damage":
					$cd = true;
					break;
				case "v":
				case "view":
					$mode = 1;
					break;
				case "del":
				case "delete":
				case "rm":
				case "remove":
					$mode = 2;
					break;
			}
		}
		switch($mode){
			case 0:
				$item = $player->getInventory()->getItemInHand();
				if($item instanceof Air){
					return "You cannot use air (hand) as your tool! Use '//{$this->getName()} rm' to delete the tool.";
				}
				$provider = $this->getMain()->getPlayerDataProvider();
				/** @var PlayerData $data */
				$data = $provider[strtolower($player->getName())];
				$id = $item->getID();
				$damage = $cd ? $item->getDamage():PlayerData::ALLOW_ANY;
				$data->setTool($this->id, new SelectedTool(
						$id, $damage, $this->getDefaultID(), $this->getDefaultDamage()));
				return "Your {$this->name} item is now $id".(is_int($damage) ? ":$damage":" (no damage value specified").".";
			case 1:
				/** @var PlayerData $data */
				$data = $this->getMain()->getPlayerDataProvider()[strtolower($player->getName())];
				$tool = $data->getTool($this->id);
				$id = $tool->getRawID();
				$damage = $tool->getRawDamage();
				return "Your {$this->name} item is $id".(is_int($damage) ? ":$damage":" (no damage value specified").".";
			default:
				/** @var PlayerData $data */
				$data = $this->getMain()->getPlayerDataProvider()[strtolower($player->getName())];
				$data->setTool($this->id, new SelectedTool(0, PlayerData::ALLOW_ANY, $this->getDefaultID(), $this->getDefaultDamage()));
				return "Your {$this->name} item has been removed.";
		}
	}
	private function getDefaultID(){
		return $this->getMain()->getConfig()->get($this->defaultPrefix."-id");
	}
	private function getDefaultDamage(){
		return $this->getMain()->getConfig()->get($this->defaultPrefix."-id");
	}
}
