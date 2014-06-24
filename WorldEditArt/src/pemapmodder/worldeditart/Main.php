<?php

namespace pemapmodder\worldeditart;

use pemapmodder\worldeditart\utils\subcommand\SubcommandMap;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\level\Position;
use pocketmine\Player;
use pocketmine\plugin\PluginBase;

class Main extends PluginBase implements Listener{
	// block touch sessions
	const BTS_NOTHING = 0;
	/** @var int[] */
	private $blockTouchSessions = [];

	// macros
	/** @var Position[] */
	private $anchors = [];

	private $recordingMacros = [];

	public function onEnable(){
		// config file
		$this->saveDefaultConfig();
		$this->getConfig(); // just to load it
		// events
		$this->getServer()->getPluginManager()->registerEvents($this, $this);
		// commands
		$this->registerCommands();
	}
	private function registerCommands(){
		$wea = new SubcommandMap("worldeditart", $this, "WorldEditArt main command", "wea.cmd", ["wea"]);
	}
	public function onJoin(PlayerJoinEvent $event){
		$this->blockTouchSessions[$event->getPlayer()->getID()] = self::BTS_NOTHING;
	}
	public function onQuit(PlayerQuitEvent $event){
		if(isset($this->blockTouchSessions[$k = $event->getPlayer()->getID()])){
			unset($this->blockTouchSessions[$k]);
		}
	}
	public function onInteract(PlayerInteractEvent $event){
		$p = $event->getPlayer();
		switch($this->blockTouchSessions[$p->getID()]){
			case "":
				break;
		}
	}
	/**
	 * @param Player $player
	 * @return Position|bool
	 */
	public function getSelectedPoint(Player $player){
		return false; // TODO
	}
}
