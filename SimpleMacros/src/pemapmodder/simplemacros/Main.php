<?php

/*
 * SimpleMacros
 * Copyright (C) 2015-2017 PEMapModder and contributors
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along
 * with this program; if not, write to the Free Software Foundation, Inc.,
 * 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301 USA.
 */

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
	/** @var RecordSession[] */
	private $recordSessions = [];
	/** @var \pocketmine\permission\PermissionAttachment[] */
	private $atts = [];

	public function onEnable(){
		$this->getServer()->getPluginManager()->registerEvents($this, $this);
		if(!is_dir($d = $this->getDataFolder())){
			mkdir($d);
		}
		if(!is_dir($d .= "macros/")){
			mkdir($d);
		}
		foreach($this->getServer()->getOnlinePlayers() as $player){
			$this->initPlayer($player);
		}
	}

	/**
	 * @param PlayerCommandPreprocessEvent $event
	 *
	 * @priority        HIGH
	 * @ignoreCancelled true
	 */
	public function onPreCmd(PlayerCommandPreprocessEvent $event){
		$line = $event->getMessage();
		if(substr($line, 0, 1) !== "/"){
			return;
		}
		if($this->commandPreprocess($event->getPlayer()->getID(), substr($line, 1))){
			$event->setCancelled();
		}
	}

	/**
	 * @param ServerCommandEvent $event
	 *
	 * @priority        MONITOR
	 * @ignoreCancelled true
	 */
	public function onConsoleCmd(ServerCommandEvent $event){
		if($this->commandPreprocess("pocketmine\\command\\ConsoleCommandSender", $event->getCommand())){
			$event->setCancelled();
		}
	}

	/**
	 * Handles a command execution event by any command senders
	 *
	 * @param string $id   identifier of command sender
	 * @param string $line command executed, without the slash
	 * @return bool whether the event should be cancelled
	 */
	public function commandPreprocess(string $id, string $line) : bool{
		if(strtolower(substr($line, 0, strlen("macro"))) === "macro" and strtolower(substr($line, 0, strlen("macro run"))) !== "macro run"){
			return false;
		}

		if(isset($this->recordSessions[$id])){
			return $this->recordSessions[$id]->handle($line);
		}
		return false;
	}

	/**
	 * @param PlayerJoinEvent $event
	 * @priority MONITOR
	 */
	public function onJoin(PlayerJoinEvent $event){
		$this->initPlayer($event->getPlayer());
	}

	/**
	 * @param PlayerQuitEvent $event
	 * @priority MONITOR
	 */
	public function onQuit(PlayerQuitEvent $event){
		$this->finalizePlayer($event->getPlayer(), true);
	}

	public function initPlayer(Player $player){
		$this->atts[$player->getId()] = $player->addAttachment($this);
	}

	public function finalizePlayer(Player $player, $isQuit){
		if(isset($this->recordSessions[$player->getId()])){
			if(!$isQuit){
				$player->sendMessage("Your recording macro has been discarded due to SimpleMacros being disabled.");
			}
			unset($this->recordSessions[$player->getId()]);
		}
		if(isset($this->atts[$player->getId()])){
			unset($this->atts[$player->getId()]);
		}
	}

	public function onCommand(CommandSender $sender, Command $cmd, $alias, array $args){
		if(!isset($args[0])){
			return false;
		}
		$id = ($sender instanceof Player) ? $sender->getId() : get_class($sender);
		switch($subcmd = strtolower(array_shift($args))){
			case "s":
			case "start":
				$this->startRecord($sender, $id, $args);
				return true;
			case "p":
			case "pause":
				$this->pauseRecord($sender, $id);
				return true;
			case "c":
			case "cont":
			case "continue":
			case "resume":
				$this->resumeRecord($sender, $id);
				return true;
			case "q":
			case "stop":
				$this->stopRecord($sender, $id, $args);
				return true;
			case "x":
			case "exe":
			case "exec":
			case "execute":
			case "r":
			case "run":
				$this->runMacro($sender, $args);
				return true;
			case "u":
			case "su":
			case "sudo":
				$this->sudoMacro($sender, $args);
				return true;
			default:
				$sender->sendMessage(/** @lang text */
					<<<EOM
Usage: /macro start|pause|resume|stop|run|sudo
/macro start [t]: Start recording a macro.
    Add a "t" after the command to execute commands while recording the macro.
    Otherwise, unless you stopped or paused recording macros, you can't run any commands.
    /macro commands are the only exception -- they won't be recorded nor blocked.
    Aliases: /macro s
/macro pause: Pause recording a macro.
    Aliases: /macro p
/macro resume: Resume recording a macro.
    Aliases: /macro c, /macro cont, /macro continue
/macro stop [.f] <name>: Stop recording a macro and save it.
    Using "ng" as the name will prevent saving.
    Adding .f before the name will overwrite the original macro with the same name, if any.
    Aliases: /macro q
/macro run <macro> <args ...>: Run a macro saved by yourself or others.
    The trailing arguments will be used to format the commands in the macro. See https://poggit.pmmp.io/p/SimpleMacros for details.
    Aliases: /macro r, /macro x, /macro exe, /macro exec, /macro execute
/macro su <macro> [.op] <target> <args ...>: Run a macro as another player
    Add .op to bypass all permission barriers (the player will be temporarily granted all permissions).
    Otherwise, some commands may not be executed, but instead show permission-denied messages.
    Aliases: /macro sudo
EOM
				);
				return true;
		}
	}

	public function startRecord(CommandSender $sender, string $id, array $args){
		if(!$sender->hasPermission("simplemacros.record")){
			$sender->sendMessage("You don't have permission to record a macro.");
			return;
		}
		if(isset($this->recordSessions[$id])){
			$sender->sendMessage("You have already started recording a macro!");
			return;
		}
		$this->recordSessions[$id] = $session = new RecordSession(in_array("t", $args), in_array("s", $args));
		$sender->sendMessage("Now recording a macro. /macro commands will not be recorded.");
		$sender->sendMessage($session->tee ? "Commands you type will be executed in addition to being recorded into the macro."
			: "Commands you type will be recorded into the macro but will not be executed.");
	}

	public function pauseRecord(CommandSender $sender, string $id){
		if(!isset($this->recordSessions[$id])){
			$sender->sendMessage("You are not recording a macro!");
			return;
		}
		if($this->recordSessions[$id]->paused){
			$sender->sendMessage("Your macro has already been stopped!");
			return;
		}
		$this->recordSessions[$id]->paused = true;
		$sender->sendMessage("You have paused recording a macro.");
	}

	public function resumeRecord(CommandSender $sender, string $id){
		if(!isset($this->recordSessions[$id])){
			$sender->sendMessage("You are not recording a macro!");
			return;
		}
		if(!$this->recordSessions[$id]->paused){
			$sender->sendMessage("Your macro was not paused!");
			return;
		}
		$this->recordSessions[$id]->paused = false;
		$sender->sendMessage("You have resumed recording a macro.");
	}

	public function stopRecord(CommandSender $sender, string $id, array $args){
		if(!isset($this->recordSessions[$id])){
			$sender->sendMessage("You don't have a recording macro to stop!");
			return;
		}
		if(!isset($args[0])){
			$sender->sendMessage("Wrong usage! Run \"/macro\" for detailed usage.");
			return;
		}
		if(strtolower($name = array_shift($args)) === "ng"){
			$sender->sendMessage("This macro has been discarded.");
			// continue to run: unset stack
		}else{
			if(strtolower($name) === ".f"){
				$overwrite = true;
				$name = array_shift($args);
			}
			if(isset($overwrite) or $this->hasMacro($name)){
				$sender->sendMessage("There is already a macro called \"$name\"!");
				$sender->sendMessage("Please use \"/macro stop\" again with another name.");
				$sender->sendMessage("Use \"/macro stop ow $name\" to overwrite the old macro.");
				return;
			}
			$success = $this->recordSessions[$id]->save($this, $name, $sender->getName());
			if(!$success){
				$sender->sendMessage("Unable to create file. Perhaps \"$name\" is not a valid filename?");
				$sender->sendMessage("Please use \"/macro stop\" again with another name.");
				return;
			}
			// continue to run: unset stack
		}
		if(isset($this->recordSessions[$id])){
			unset($this->recordSessions[$id]);
		}
		$sender->sendMessage("You have successfully saved macro \"$name\".");
	}

	public function runMacro(CommandSender $sender, array $args){
		if(!$sender->hasPermission("simplemacros.run")){
			$sender->sendMessage("You don't have permission to run a macro.");
			return;
		}
		if(!isset($args[0])){
			$sender->sendMessage("Usage: /macro run <name> <args ...>");
			return;
		}
		$name = array_shift($args);
		foreach($this->getMacro($name, $sprintf) as $line){
			$line = trim($line);
			if($sprintf){
				try{
					/** @noinspection PhpUsageOfSilenceOperatorInspection */
					$line = @sprintf($line, ...$args);
				}catch(\Throwable $e){
				}
				if($line === false){
					$sender->sendMessage("Unable to execute line \"$line\" because you provided too few arguments");
				}
			}
			$this->getServer()->dispatchCommand($sender, $line);
		}
	}

	public function sudoMacro(CommandSender $sender, array $args){
		if(!$sender->hasPermission("simplemacros.sudo")){
			$sender->sendMessage("You don't have permision to use macro sudo.");
			return;
		}
		if(!isset($args[1])){
			$sender->sendMessage("Wrong usage! Run \"/macro\" for detailed usage.");
			return;
		}
		try{
			$macro = $this->getMacro($name = array_shift($args), $sprintf);
		}catch(\RuntimeException $e){
			$sender->sendMessage("Macro doesn't exist.");
			return;
		}
		if($asOp = $args[0] === ".op"){
			array_shift($args);
		}
		$target = $this->getServer()->getPlayer($targetName = array_shift($args));
		if(!($target instanceof Player)){
			$sender->sendMessage("Player \"$targetName\" not found.");
			return;
		}
		if(strtolower($target->getName()) !== strtolower($targetName)){
			$sender->sendMessage("Interpreted \"$targetName\" as \"{$target->getName()}\".");
		}
		if($asOp && !$sender->hasPermission("simplemacros.opsudo")){
			$sender->sendMessage("You do not have permission to op-sudo a player with a macro!");
			return;
		}
		if($asOp){
			$perms = [];
			foreach($this->getServer()->getPluginManager()->getPermissions() as $perm){
				if(!$target->hasPermission($permName = $perm->getName())){
					$perms[$permName] = true;
				}
			}
			$this->atts[$target->getId()]->setPermissions($perms);
		}
		foreach($macro as $line){
			$line = trim($line);
			if($sprintf){
				try{
					/** @noinspection PhpUsageOfSilenceOperatorInspection */
					$line = @sprintf($line, ...$args);
				}catch(\Throwable $e){
				}
				if($line === false){
					$sender->sendMessage("Unable to execute line \"$line\" because you provided too few arguments");
				}
			}
			$this->getServer()->dispatchCommand($target, $line);
		}
		if($asOp and isset($perms)){
			$this->atts[$target->getId()]->unsetPermissions(array_keys($perms));
		}
		$sender->sendMessage(count($macro) . " commands run as " . $target->getName() . ($asOp ? " bypassing permission limits" : "") . ".");
	}

	/**
	 * @param string $name
	 * @param bool   $sprintf
	 * @return array|\string[]
	 */
	public function getMacro(string $name, bool &$sprintf = false) : array{
		if(is_file($path = $this->getDataFolder() . "macros/$name.txt")){
			$ret = explode("\n", file_get_contents($path));
			$sprintf = false;
			foreach($ret as $i => $line){
				if($line{0} === "#"){
					if($line === "#sprintf enabled"){
						$sprintf = true;
					}
					unset($ret[$i]);
				}
			}
			return array_values($ret);
		}
		throw new \RuntimeException("Not found");
	}

	public function hasMacro(string $name) : bool{
		return is_file($this->getDataFolder() . "macros/$name.txt");
	}

	public function onDisable(){
		foreach($this->getServer()->getOnlinePlayers() as $p){
			$this->finalizePlayer($p, false);
		}
	}
}
