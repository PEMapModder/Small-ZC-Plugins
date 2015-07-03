<?php

namespace pemapmodder\cmdsel;

use pemapmodder\cmdsel\event\PlayerCommandPreprocessEvent_sub;
use pemapmodder\cmdsel\event\RemoteServerCommandEvent_sub;
use pemapmodder\cmdsel\event\ServerCommandEvent_sub;
use pemapmodder\cmdsel\selector\RecursiveSelector;
use pemapmodder\cmdsel\selector\Selector;

use pemapmodder\cmdsel\selector\AllRecursiveSelector;

use pemapmodder\cmdsel\selector\NearestSelector;
use pemapmodder\cmdsel\selector\RandomSelector;
use pemapmodder\cmdsel\selector\UsernameSelector;
use pemapmodder\cmdsel\selector\WorldSelector;

use pocketmine\Player;
use pocketmine\command\CommandSender;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerCommandPreprocessEvent;
use pocketmine\event\player\PlayerChatEvent;
use pocketmine\event\server\RemoteServerCommandEvent;
use pocketmine\event\server\ServerCommandEvent;
use pocketmine\level\Position;
use pocketmine\plugin\PluginBase;
use pocketmine\Server;
use pocketmine\event\Timings;

const DEBUGGING = true;

class CmdSel extends PluginBase implements Listener{
	/** @var Selector[] */
	private $selectors = [];
	/** @var RecursiveSelector[] */
	private $recursiveSelectors = [];
	public function onEnable(){
		$this->getServer()->getPluginManager()->registerEvents($this, $this);
		$this->registerRecursiveSelector(new AllRecursiveSelector);
		$this->registerSelector(new NearestSelector);
		$this->registerSelector(new RandomSelector);
		$this->registerSelector(new UsernameSelector);
		$this->registerSelector(new WorldSelector);
	}
	public function registerSelector(Selector $selector){
		$this->selectors[$selector->getName()] = $selector;
		foreach($selector->getAliases() as $alias){
			$this->selectors[$alias] = $selector;
		}
	}
	public function registerRecursiveSelector(RecursiveSelector $selector){
		$this->recursiveSelectors[$selector->getName()] = $selector;
		foreach($selector->getAliases() as $alias){
			$this->recursiveSelectors[$alias] = $selector;
		}
	}
	/**
	 * @param ServerCommandEvent $event
	 * @priority HIGHEST
	 * @ignoreCancelled true
	 */
	public function onConsoleCmd(ServerCommandEvent $event){
		if($event instanceof ServerCommandEvent_sub){
			return;
		}
		/** @var string|array $cmd */
		$cmd = $event->getCommand();
		if(DEBUGGING){
			echo "Processing console command $cmd... ";
		}
		if($this->proceedCommand($event->getSender(), $cmd)){
			if(DEBUGGING){
				echo "Parsed command recursively: ";
				var_dump($cmd);
				echo PHP_EOL;
			}
			$event->setCancelled();
			if(count($cmd) > 0){
				foreach($cmd as $c){
					$this->getServer()->getPluginManager()->callEvent($ev = new ServerCommandEvent_sub($event->getSender(), $c));
					if(!$ev->isCancelled()){
						$this->getServer()->dispatchCommand($ev->getSender(), $ev->getCommand());
					}
				}
			}
		}
		else{
			$event->setCommand($cmd);
			if(DEBUGGING){
				echo "Command processed and changed to:\n$cmd\n";
			}
		}
	}
	/**
	 * @param RemoteServerCommandEvent $event
	 * @priority HIGHEST
	 * @ignoreCancelled true
	 */
	public function onRCONCmd(RemoteServerCommandEvent $event){
		if($event instanceof RemoteServerCommandEvent_sub){
			return;
		}
		/** @var string|array $cmd */
		$cmd = $event->getCommand();
		if($this->proceedCommand($event->getSender(), $cmd)){
			if(count($cmd) > 0){
				//$event->setCommand(array_shift($cmd));
				$event->setCancelled();
				foreach($cmd as $c){
					$this->getServer()->getPluginManager()->callEvent($ev = new RemoteServerCommandEvent_sub($event->getSender(), $c));
					if(!$ev->isCancelled()){
						$this->getServer()->dispatchCommand($ev->getSender(), $ev->getCommand());
					}
				}
			}
		}
		else{
			$event->setCommand($cmd);
		}
	}
	public function onPlayerCmd(PlayerCommandPreprocessEvent $event){
		if($event instanceof PlayerCommandPreprocessEvent_sub){
			return;
		}
		$line = $event->getMessage();
		if(substr($line, 0, 1) === "/"){
			$cmd = substr($line, 1);
			if($this->proceedCommand($event->getPlayer(), $cmd)){ // if recursive; $cmd must be changed to array
				if(count($cmd) > 0){
					$event->setCancelled();
					//$event->setMessage("/".array_shift($cmd));
					foreach($cmd as $c){
						$this->getServer()->getPluginManager()->callEvent($ev = new PlayerCommandPreprocessEvent_sub($event->getPlayer(), ".".$c));
						if($ev->isCancelled()){
							continue;
						}
						Timings::$playerCommandTimer->startTiming();
						$this->getServer()->dispatchCommand($ev->getPlayer(), substr($ev->getMessage(), 1));
						Timings::$playerCommandTimer->stopTiming();
					}
				}
				else{
					$event->setCancelled();
				}
			}
			else{
				$event->setMessage("/$cmd");
			}
		}
	}
	public function proceedCommand(CommandSender $sender, &$line){
		$tokens = explode(" ", $line);
		$first = true;
		foreach($tokens as $offset => $arg){
			if($first){
				$first = false;
				continue;
			}
			if(substr($arg, 0, 1) === "@"){
				$selector = substr($arg, 1);
				$args = [];
				if(strpos($selector, "[") !== false){
					$name = strstr($selector, "[", true);
					$preArgs = explode(",", substr(strstr($selector, "["), 1, -1));
					foreach($preArgs as $rawArg){
						$splited = explode("=", $rawArg, 2);
						if(count($splited) === 1){
							array_unshift($splited, "");
						}
						$args[] = $splited;
					}
				}
				else{
					$name = $selector;
				}
				if(isset($this->selectors[$name])){
					$results = $this->selectors[$name]->format($this->getServer(), $sender, $name, $args);
					if(is_string($results)){
						$tokens[$offset] = $results;
					}
				}
			}
		}
		$first = true;
		foreach($tokens as $offset => $arg){
			if($first){
				$first = false;
				continue;
			}
			if(substr($arg, 0, 1) === "@"){
				$selector = substr($arg, 1);
				$args = [];
				if(strpos($selector, "[") !== false){
					$name = strstr($selector, "[", true);
					$preArgs = explode(",", substr(strstr($selector, "["), 1, -1));
					foreach($preArgs as $rawArg){
						$splited = explode("=", $rawArg, 2);
						if(count($splited) === 1){
							array_unshift($splited, "");
						}
						$args[] = $splited;
					}
				}
				else{
					$name = $selector;
				}
				if(isset($this->recursiveSelectors[$name])){
					$results = $this->recursiveSelectors[$name]->format($this->getServer(), $sender, $name, $args);
					if(is_array($results)){
						$line = [];
						foreach($results as $result){
							$tmpLine = $tokens;
							$tmpLine[$offset] = $result;
							$line[] = implode(" ", $tmpLine);
						}
						return true;
					}
				}
			}
		}
		$line = implode(" ", $tokens);
		return false;
	}
	public static function checkSelectors(array $args, CommandSender $sender, Player $player){
		foreach($args as $name => $value){
			switch($name){
				case "x":
				case "y":
				case "z":
					if(isset($args["d" . $name])){
						break;
					}
					$delta = 0;
					if($value{0} === "~" and $sender instanceof Position){
						$delta += $player->{$name};
					}
					$actual = $sender->{$name};
					if(((int) $delta) !== ((int) $actual)){
						return false;
					}
					break;
				case "r":
					if($sender instanceof Position){
						if($sender->distance($player) > floatval($value)){
							return false;
						}
						break;
					}
					return false;
				case "rm":
					if($sender instanceof Position){
						if($sender->distance($player) < floatval($value)){
							return false;
						}
						break;
					}
					return false;
				case "m":
					$mode = intval($value);
					if($mode === -1){
						break; // what is the point of adding this (in PC) when they can just safely leave this out?
					}
					if($mode !== $player->getGamemode()){
						return false;
					}
					break;
				case "name":
					if($value !== $sender->getName()){
						return false;
					}
					break;
				case "name!":
					if($value === $sender->getName()){
						return false;
					}
					break;
				// TODO argument "c" (count)
				case "rx":
					if($player->yaw > floatval($value)){
						return false;
					}
					break;
				case "rxm":
					if($player->yaw < floatval($value)){
						return false;
					}
					break;
				case "ry":
					if($player->pitch > floatval($value)){
						return false;
					}
					break;
				case "rym":
					if($player->pitch < floatval($value)){
						return false;
					}
					break;
			}
		}
		foreach(["x", "y", "z"] as $v){
			if(isset($args["d" . $v])){
				if(isset($args[$v])){
					$from = (int) $args[$v];
				}
				elseif($sender instanceof Position){ // lower priority
					$from = $sender->{$v};
				}
				else{
					continue;
				}
				$to = (int) $args["d" . $v];
				$actual = $player->{$v};
				if($from <= $actual and $actual <= $to){
					break;
				}
				return false;
			}
		}
		return true;
	}
	/**
	 * @param Position $center
	 * @param callable[] $exceptions
	 * @return array
	 */
	public static function getNearestPlayers(Position $center, array $exceptions = []){
		$currentDistance = PHP_INT_MAX;
		$nearest = [];
		foreach($center->getLevel()->getPlayers() as $player){
			foreach($exceptions as $e){
				if(call_user_func($e, $player) === false){
					$continue = true;
					break;
				}
			}
			if(isset($continue)){
				continue;
			}
			if($player === $center){
				continue;
			}
			if($center->distance($player) === $currentDistance){
				$nearest[] = $player;
			}
			elseif($center->distance($player) < $currentDistance){
				$nearest = [$player];
			}
		}
		return $nearest;
	}
}
