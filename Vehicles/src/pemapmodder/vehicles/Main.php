<?php

namespace pemapmodder\vehicles;

use pocketmine\command\CommandSender;
use pocketmine\command\ConsoleCommandSender;
use pocketmine\event\entity\EntityMoveEvent;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\command\Command;
use pocketmine\event\Listener;
use pocketmine\plugin\PluginBase as Base;
use pocketmine\Server;

class Main extends Base implements Listener{
	/**
	 * @var string $path path for saving sessions
	 */
	private $path;
	private $types;
	private $sessions = [];
	public function onEnable(){
		$this->getServer()->getPluginManager()->registerEvents($this, $this);
		@mkdir($this->path = $this->getDataFolder()."savedSessions/");

	}
	public function onCommand(CommandSender $sender, Command $cmd, $label, array $args){
		switch($cmd->getName()){
			case "vehicle":
				if($sender instanceof ConsoleCommandSender){
					$sender->sendMessage("Please run this command in-game.");
					return true;
				}
				break;
		}
		return false;
	}
	public function onJoin(PlayerJoinEvent $event){
		$v = $this->loadSession($event->getPlayer()->getName());
		eval("\$this->sessions[\$event->getPlayer()->CID] = $v;");
	}
	public function onQuit(PlayerQuitEvent $event){
		$v = var_export($this->sessions[$event->getPlayer()->CID], true);
		$this->saveSession($event->getPlayer()->getName(), $v);
	}
	/**
	 * @param PlayerInteractEvent $event
	 * @priority LOW
	 */
	public function onBlockTouch(PlayerInteractEvent $event){

	}
	public function onMove(EntityMoveEvent $event){
		$event->getVector();
	}
	public function saveSession($name, $session){
		file_put_contents($this->path.strtolower($name).".txt", $session === false ? "false":$session);
	}
	public function loadSession($name){
		$session = @file_get_contents($this->path.strtolower($name).".txt");
		if(!is_string($session)){
			return false;
		}
		if(!is_subclass_of($session, "pemapmodder\\vehicles\\Vehicle")){
			return false;
		}
		return $session;
	}
	public static function register(VehicleType $type){
		self::getInstance()->registerType($type);
	}
	public function registerType(VehicleType $type){
		$this->types[$type->getClass()] = $type;
	}
	/**
	 * @return self
	 */
	public static function getInstance(){
		return Server::getInstance()->getPluginManager()->getPlugin("Vehicles");
	}
}
