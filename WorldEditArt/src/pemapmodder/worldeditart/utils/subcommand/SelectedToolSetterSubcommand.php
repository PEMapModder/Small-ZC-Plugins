<?php

namespace pemapmodder\worldeditart\utils\subcommand;

use pemapmodder\worldeditart\Main;
use pemapmodder\worldeditart\utils\provider\player\PlayerData;
use pemapmodder\worldeditart\utils\provider\player\SelectedTool;
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
		return "[cd|check-damage|v|view]";
	}
	public function getDescription(){
		return "Set/view own's {$this->name} tool";
	}
	public function checkPermission(/** @noinspection PhpUnusedParameterInspection */
		Player $player){
		return true; // TODO
	}
	public function onRun(array $args, Player $player){
		$cd = false;
		$mode = 0; // 0 for set hand, 1 for view
		while(isset($args[0])){
			$arg = $args[0];
			switch($arg){
				case "cd":
				case "check-damage":
					$cd = true;
					break;
				case "v":
				case "view":
					$mode = 1;
					break;
			}
		}
		switch($mode){
			case 0:
				$item = $player->getInventory()->getItemInHand();
				$provider = $this->getMain()->getPlayerDataProvider();
				/** @var \pemapmodder\worldeditart\utils\provider\player\PlayerData $data */
				$data = $provider[strtolower($player->getName())];
				$id = $item->getID();
				$damage = $cd ? $item->getDamage():PlayerData::ALLOW_ANY;
				$data->setTool($$this->id, new SelectedTool(
						$id, $damage, $this->getDefaultID(), $this->getDefaultDamage()));
				return "Your {$this->name} item is now $id".(is_int($damage) ? ":$damage":" (no damage value specified").".";
			default:
				/** @var PlayerData $data */
				$data = $this->getMain()->getPlayerDataProvider()[strtolower($player->getName())];
				$tool = $data->getTool($this->id);
				$id = $tool->getRawID();
				$damage = $tool->getRawDamage();
				return "Your {$this->name} item is $id".(is_int($damage) ? ":$damage":" (no damage value specified").".";
				break;
		}
	}
	private function getDefaultID(){
		return $this->getMain()->getConfig()->get($this->defaultPrefix."-id");
	}
	private function getDefaultDamage(){
		return $this->getMain()->getConfig()->get($this->defaultPrefix."-id");
	}
}
