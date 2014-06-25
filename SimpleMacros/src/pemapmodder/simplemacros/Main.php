<?php

namespace pemapmodder\simplemacros;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerCommandPreprocessEvent;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\event\server\ServerCommandEvent;
use pocketmine\Player;
use pocketmine\plugin\PluginBase;

class Main extends PluginBase implements Listener{
	private $stack = [];
	private $paused = [];
	/** @var \pocketmine\permission\PermissionAttachment[] */
	private $atts = [];
	public function onEnable(){
		$this->getServer()->getPluginManager()->registerEvents($this, $this);
		@mkdir($this->getDataFolder());
		@mkdir($this->getDataFolder()."macros/");
	}
	/**
	 * @param PlayerCommandPreprocessEvent $event
	 * @priority MONITOR
	 * @ignoreCancelled true
	 */
	public function onPreCmd(PlayerCommandPreprocessEvent $event){
		$line = $event->getMessage();
		if(substr($line, 0, 1) !== "/"){
			return;
		}
		$this->onCmd($event->getPlayer()->getID(), substr($line, 1));
	}
	/**
	 * @param ServerCommandEvent $event
	 * @priority MONITOR
	 * @ignoreCancelled true
	 */
	public function onConsoleCmd(ServerCommandEvent $event){
		$this->onCmd("pocketmine\\command\\ConsoleCommandSender", $event->getCommand());
	}
	private function onCmd($id, $line){
		if(strtolower(substr($line, 0, strlen("macro"))) === "macro" and strtolower(substr($line, 0, strlen("macro run"))) !== "macro run"){
			return;
		}
		if(isset($this->stack[$id]) and @$this->paused[$id] !== true){
			$this->stack[$id] .= "$line\n";
		}
	}
	public function onJoin(PlayerJoinEvent $event){
		$this->atts[$event->getPlayer()->getID()] = $event->getPlayer()->addAttachment($this);
	}
	public function onQuit(PlayerQuitEvent $event){
		$this->finalizePlayer($event->getPlayer(), true);
	}
	public function onCommand(CommandSender $sender, Command $cmd, $alias, array $args){
		if(!isset($args[0])) return false;
		$k = ($sender instanceof Player) ? $sender->getID():get_class($sender);
		switch($subcmd = array_shift($args)){
			case "stop":
				if(!isset($this->stack[$k])){
					$sender->sendMessage("You don't have a recording macro to stop!");
				}
				if(!isset($args[0])){
					$sender->sendMessage("Usage: /macro stop <name>");
					$sender->sendMessage("Don't worry, this command will not be recorded.");
					break;
				}
				if(strtolower($name = array_shift($args)) === "ng"){
					$sender->sendMessage("This NG macro has been discarded.");
					return true;
				}
				if($this->getMacro($name) !== false){
					$sender->sendMessage("There is already a macro called $name!");
					$sender->sendMessage("Don't worry, this command will not be recorded.");
					break;
				}
				$this->saveMacro($name, $this->stack[$k]);
				if(isset($this->stack[$id = $k])){ // copied xD
					unset($this->stack[$id]); // avoid bugs if the entity ID gets reused
				}
				if(isset($this->paused[$id])){
					unset($this->stack[$id]);
				}
				$sender->sendMessage("You have successfully saved macro $name.");
				break;
			case "start":
				if(!$sender->hasPermission("simplemacros.record")){
					$sender->sendMessage("You don't have permission to record a macro.");
					return true;
				}
				if(isset($this->stack[$k])){
					$sender->sendMessage("You have already started recording a macro!");
				}
				$this->stack[$k] = "";
				$sender->sendMessage("You are now recording a macro.");
				break;
			case "pause":
				if(!isset($this->stack[$k])){
					$sender->sendMessage("You are not recording a macro!");
				}
				$this->paused[$k] = true;
				$sender->sendMessage("Your macro is now paused.");
				$sender->sendMessage("Don't worry, this command will not be recorded.");
				break;
			case "resume":
				if(!isset($this->paused[$k]) or $this->paused[$k] === false){
					$sender->sendMessage("You do not have a paused recording macro!");
					$sender->sendMessage("Don't worry, this command will not be recorded.");
					return true;
				}
				$this->paused[$k] = false;
				$sender->sendMessage("You have now resumed your macro.");
				break;
			case "sudo":
				if(!$sender->hasPermission("simplemacros.sudo")){
					$sender->sendMessage("You don't have permision to sudo others with a macro.");
					return true;
				}
				if(!isset($args[1])){
					$sender->sendMessage("Usage: /macro sudo <macro name> <player name> [-op]");
					return true;
				}
				$macro = $this->getMacro($name = array_shift($args));
				if($macro === false){
					$sender->sendMessage("$macro doesn't exist!");
					return true;
				}
				if(!(($p = $this->getServer()->getPlayer($args[1])) instanceof Player)){
					$sender->sendMessage("Player $args[1] not found!");
					return true;
				}
				if(isset($args[2]) and $args[2] === "-op" and !$sender->hasPermission("simplemacros.opsudo")){
					$sender->sendMessage("You do not have permission to op-sudo a player with a macro!");
				}
				foreach($macro as $rline){
					$line = trim($rline);
					if(isset($args[2]) and $args[2] === "-op"){
						if(($rcmd = $this->getServer()->getCommandMap()->getCommand(strstr($line, " ", true))) instanceof Command){
							if(!$cmd->testPermissionSilent($p) and $cmd->testPermissionSilent($sender)){
								$perms = [$rcmd->getPermission()];
								if(strpos($perms[0], ";") !== false){
									$perms = explode(";", $perms[0]);
								}
								foreach($perms as $perm){
									$this->atts[$k]->setPermission($perm, true);
								}
							}
						}
					}
					$this->getServer()->dispatchCommand($p, $line);
					if(isset($perms)){
						foreach($perms as $perm){
							$this->atts[$k]->unsetPermission($perm);
						}
					}
				}
				$sender->sendMessage("Command run as ".$p->getName().".");
				break;
			case "run":
				if(!$sender->hasPermission("simplemacros.run")){
					$sender->sendMessage("You don't have permission to run a macro.");
					return true;
				}
				if(!isset($args[0])){
					$sender->sendMessage("Usage: /macro run <name>");
					return true;
				}
				foreach($this->getMacro($args[0]) as $line){
					$this->getServer()->dispatchCommand($sender, $line);
				}
				break;
			default:
				return false;
		}
		return true;
	}
	public function saveMacro($name, $lines){
		file_put_contents($this->getDataFolder()."macros/$name.txt", $lines);
	}
	public function getMacro($name){
		if(is_file($path = $this->getDataFolder()."macros/$name.txt")){
			return explode("\n", file_get_contents($path));
		}
		return false;
	}
	public function onDisable(){
		foreach($this->getServer()->getOnlinePlayers() as $p){
			$this->finalizePlayer($p, false);
		}
	}
	public function finalizePlayer(Player $player, $isQuit){
		if(isset($this->stack[$id = $player->getID()])){
			unset($this->stack[$id]); // avoid bugs if the entity ID gets reused
			if($isQuit){
				$this->getLogger()->alert($player->getDisplayName()." has quit, so his recording macro has been discarded.");
			}
			else{
				$this->getLogger()->alert("Discarding recording macro of ".$player->getDisplayName()." due to plugin disable.");
				$player->sendMessage("Your recording macro has been discarded due to SimpleMacros being disabled.");
			}
		}
		if(isset($this->paused[$id])){
			unset($this->stack[$id]);
		}
		if(isset($this->atts[$id])){
			unset($this->atts[$id]);
		}
	}
}
