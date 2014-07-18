<?php

namespace pemapmodder\worldeditart\utils\subcommand;

use pemapmodder\worldeditart\Main;
use pemapmodder\worldeditart\utils\spaces\Space;
use pocketmine\command\CommandSender;
use pocketmine\command\ConsoleCommandSender;
use pocketmine\level\Position;
use pocketmine\Player;

abstract class Subcommand{
	const ALL = 0;
	const CONSOLE = 1;
	const PLAYER = 2;
	const SELECTED = 3;
	const SEL_SPACE = 4;
	const NO_PERM = true;
	const WRONG_USE = false;
	const NO_PLAYER = 3;
	const NO_SELECTION = 4;
	const NO_SELECTED_POINT = 5;
	const NO_BLOCK = 6;
	const NO_ITEM = 7;
	const NO_ANCHOR = 8;
	protected $main;
	private $callable, $permCheck;
	private $issuer = self::ALL;
	/**
	 * @param Main $main
	 * @param string $callable
	 * @param string $permCheck
	 */
	public function __construct(Main $main, $callable = "onRun", $permCheck = "checkPermission"){
		$this->main = $main;
		$rc = new \ReflectionClass($this);
		$this->callable = $callable;
		$this->permCheck = $permCheck;
		try{
			$method = $rc->getMethod($permCheck); // I think @shoghicp will say that there would be undefined behaviour again...
			$args = $method->getParameters();
			if(isset($args[0])){
				$class = $args[0]->getClass();
				if($class instanceof \ReflectionClass){
					switch($class->getName()){
						case "pocketmine\\Player":
							$this->issuer = self::PLAYER;
							break;
						case "pocketmine\\command\\ConsoleCommandSender":
							$this->issuer = self::CONSOLE;
							break;
						case "pocketmine\\level\\Position":
							$this->issuer = self::SELECTED;
							break;
						case "pemapmodder\\worldeditart\\utils\\spaces\\Space":
							$this->issuer = self::SEL_SPACE;
							break;
					}
				}
			}
		}
		catch(\ReflectionException $ex){
			trigger_error(get_class($this) . " passed constructor to parent constructor with invalid argument 4 callable \"$callable\"", E_USER_ERROR);
			return;
		}
	}
	// I made these functions final to avoid accidental override
	public final function run(array $args, CommandSender $sender){
		if($this->issuer === self::CONSOLE and !($sender instanceof ConsoleCommandSender)){
			$sender->sendMessage("Please run this command in-game.");
			return;
		}
		if(($this->issuer === self::PLAYER or $this->issuer === self::SELECTED) and !($sender instanceof Player)){
			$sender->sendMessage("Please run this command on-console.");
			return;
		}
		if($this->issuer === self::SELECTED){
			$p = $this->main->getSelectedPoint($sender);
			if($p instanceof Position){
				$result = call_user_func(array($this, $this->callable), $args, $p, $sender);
			}
			else{
				$result = self::NO_SELECTED_POINT;
			}
		}
		elseif($this->issuer === self::SEL_SPACE){
			$space = $this->main->getSelection($sender);
			if($space instanceof Space){
				$result = call_user_func(array($this, $this->callable), $args, $space, $sender);
			}
			else{
				$result = self::NO_SELECTION;
			}
		}
		else{
			$result = call_user_func(array($this, $this->callable), $args, $sender);
		}
		if(is_string($result)){
			$sender->sendMessage($result);
			return;
		}
		switch($result){
			case self::WRONG_USE:
				$sender->sendMessage("Usage: {$this->getUsage()}");
				break;
			case self::NO_PERM:
				$sender->sendMessage("You don't have permission to do this!");
				break;
			case self::NO_PLAYER:
				$sender->sendMessage("Player not found!");
				break;
			case self::NO_SELECTION:
				$sender->sendMessage("You must make a selection first!");
				break;
			case self::NO_SELECTED_POINT:
				$sender->sendMessage("You must select a point first!");
				break;
			case self::NO_BLOCK:
				$sender->sendMessage("Block not found!");
				break;
			case self::NO_ITEM:
				$sender->sendMessage("Item not found!");
				break;
			case self::NO_ANCHOR:
				$sender->sendMessage("You don't have an anchor set!");
				break;
		}
		return;
	}
	public final function getMain(){
		return $this->main;
	}
	public abstract function getName();
	public abstract function getDescription();
	public abstract function getUsage();
	/**
	 * @param CommandSender $sender
	 * @return bool
	 */
	public final function hasPermission(CommandSender $sender){
		$callable = array($this, $this->permCheck);
		if($this->issuer === self::CONSOLE and !($sender instanceof ConsoleCommandSender) or $this->issuer === self::PLAYER and !($sender instanceof Player)){
			return false; // wrong command sender
		}
		if($this->issuer === self::SELECTED){
			if(!($sender instanceof Player) or !(($p = $this->main->getSelectedPoint($sender)) instanceof Position)){
				return true;
			}
			return call_user_func($callable, $p, $sender);
		}
		if($this->issuer === self::SEL_SPACE){
			if(!($sender instanceof Player) or !(($space = $this->main->getSelection($sender)) instanceof Space)){
				return true;
			}
			return call_user_func($callable, $space, $sender);
		}
		return call_user_func($callable, $sender);
	}
}
