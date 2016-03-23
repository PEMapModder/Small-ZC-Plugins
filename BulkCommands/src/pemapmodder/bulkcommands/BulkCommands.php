<?php

namespace pemapmodder\bulkcommands;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerCommandPreprocessEvent;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\Player;
use pocketmine\plugin\PluginBase;

class BulkCommands extends PluginBase implements Listener{
	/** @var BulkCommandSession[] */
	private $sessions = [];

	public function onEnable(){
		$this->getServer()->getPluginManager()->registerEvents($this, $this);
		foreach($this->getServer()->getOnlinePlayers() as $p){
			$this->startSession($p);
		}
	}

	public function onPlayerJoin(PlayerJoinEvent $event){
		$this->startSession($event->getPlayer());
	}

	public function onPlayerQuit(PlayerQuitEvent $event){
		$this->endSession($event->getPlayer());
	}

	public function onDisable(){
		foreach($this->getServer()->getOnlinePlayers() as $p){
			$this->endSession($p);
		}
	}

	public function startSession(Player $player){
		$this->sessions[$player->getId()] = new BulkCommandSession;
	}

	public function endSession(Player $player){
		if(isset($this->sessions[$player->getId()])){
			unset($this->sessions[$player->getId()]);
		}
	}

	public function getSession(Player $player){
		return $this->sessions[$player->getId()];
	}

	public function onCommand(CommandSender $sender, Command $cmd, $l, array $args){
		if($cmd->getName() !== "bulkcmd"){
			return false;
		}
		if(!($sender instanceof Player)){
			$sender->sendMessage("Please run this command in-game.");
			return true;
		}
		$send = !$sender->hasPermission("bulkcommands.silent");
		$session = $this->getSession($sender);
		if($session->format !== null){
			$session->format = null;
			$session->cnt = 0;
			if($send){
				$sender->sendMessage("BulkCommands has been turned off for you.");
			}
			return true;
		}
		if(!isset($args[0])){
			return false;
		}
		$format = implode(" ", $args);
		if(substr($format, 0, 1) !== "/" and !$sender->hasPermission("bulkcommands.noslash")){
			if($send){
				$sender->sendMessage("You don't have permission to use BulkCommands without the format starting with a slash!");
			}
			return true;
		}
		$session->format = $format;
		$session->cnt = substr_count($session->format, "%s"); // Having more than required is OK. Although it is slower, it is still faster than counting occurrences escaped.
		if($session->cnt === 0){
			if($send){
				$sender->sendMessage("Warning: There is no \"%s\" in the format! All your chat messages will have the same output!");
			}
		}
		$sender->sendMessage("Your BulkCommands format has been set to \"$session->format\".");
		try{
			$sender->sendMessage("Sample: Typing \"foo\" in chat will become " . sprintf($session->format, ...array_fill(0, $session->cnt, "foo")));
		}catch(\RuntimeException $e){
			$sender->sendMessage("An error occurred while testing your format! Message: " . $e->getMessage());
			$sender->sendMessage("Remember, use %% to represent %!");
			$session->format = null;
			$session->cnt = 0;
			$sender->sendMessage("Your format has been reset.");
		}
		return true;
	}

	public function onPlayerCmdPreprocess(PlayerCommandPreprocessEvent $event){
		$session = $this->getSession($event->getPlayer());
		if($session->format !== null and substr($event->getMessage(), 0, 1) !== "/"){
			$event->setMessage($session->format($event->getMessage()));
		}
	}
}

