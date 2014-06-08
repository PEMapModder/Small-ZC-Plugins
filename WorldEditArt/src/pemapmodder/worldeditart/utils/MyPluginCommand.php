<?php

namespace pemapmodder\worldeditart\utils;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\command\PluginIdentifiableCommand;
use pocketmine\plugin\Plugin;

class MyPluginCommand extends Command implements PluginIdentifiableCommand{
	const NO_PERM = 0;
	const CONSOLE = 1;
	const IN_GAME = 2;
	const NO_SEL = 3;
	const NO_PLAYER = 4;
	const ANY = 0;
	const CONSOLE_ONLY = 1;
	const IN_GAME_ONLY = 2;
	private $state = self::ANY;
	public function __construct($name, Plugin $plugin, callable $exe){
		$this->exe = $exe;
		$this->plugin = $plugin;
		$class = new \ReflectionClass($exe[0]);
		$method = $class->getMethod($exe[1]);
		if(isset($method->getParameters()[2])){
			if($method->getParameters()[2]->getDeclaringClass()->getName() === "pocketmine\\Player"){
				$this->setState(self::IN_GAME_ONLY);
			}
			elseif($method->getParameters()[2]->getDeclaringClass()->getName() === "pocketmine\\command\\ConsoleCommandSender"){
				$this->setState(self::CONSOLE_ONLY);
			}
		}
	}
	public function setState($state){
		$this->state = $state;
	}
	public function isInGameOnly(){
		return $this->state === self::IN_GAME_ONLY;
	}
	public function isOnConsoleOnly(){
		return $this->state === self::CONSOLE_ONLY;
	}
	public function getPlugin(){
		return $this->plugin;
	}
	public function execute(CommandSender $issuer, $lbl, array $args){
		if($this->isInGameOnly()){
			$data = self::IN_GAME;
		}
		elseif($this->isOnConsoleOnly()){
			$data = self::CONSOLE;
		}
		else{
			$data = call_user_func($this->exe, $this->getName(), $args, $issuer);
		}
		switch(true){
			case is_bool($data):
				if($data === false){
					$issuer->sendMessage($this->getUsage());
				}
				return true;
			case is_int($data):
				switch($data){
					case self::NO_PERM:
						$issuer->sendMessage("You don't have permission to use this command.");
						break;
					case self::CONSOLE:
						$issuer->sendMessage("Please run this command on-console.");
						break;
					case self::IN_GAME:
						$issuer->sendMessage("Please run this command in-game.");
						break;
					case self::NO_SEL:
						$issuer->sendMessage("You need to make a selection first!");
						break;
					case self::NO_PLAYER:
						$issuer->sendMessage("Player not found!");
						break;
				}
				return true;
			case is_string($data):
				$issuer->sendMessage($data);
				return true;
		}
		return true;
	}
}
