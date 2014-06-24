<?php

namespace pemapmodder\worldeditart;

use pemapmodder\worldeditart\utils\Macro;
use pemapmodder\worldeditart\utils\MyPluginCommand;
use pemapmodder\worldeditart\utils\spaces\CuboidSpace;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\item\Item;
use pocketmine\level\Position;
use pocketmine\Player;
use pocketmine\plugin\PluginBase;
use pemapmodder\worldeditart\utils\spaces\Space;

class Main extends PluginBase implements Listener{
	// block touch sessions
	const BTS_NOTHING = 0;
	/** @var int[] */
	private $blockTouchSessions = [];

	// macros
	/** @var Position[] */
	private $anchors = [];

	private $macros = [];

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
		$macro = new MyPluginCommand("macro", $this, array($this, "onMacroCmd"));
		$macro->setDescription("WorldEditArt macros managing");
		$macro->setUsage("/macro <start|end|run|anchor|list> [macro name]");
		$this->getServer()->getCommandMap()->registerAll("worldeditart", [$macro]);
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

	}
}
