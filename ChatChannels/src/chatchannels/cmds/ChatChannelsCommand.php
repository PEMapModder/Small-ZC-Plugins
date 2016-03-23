<?php

namespace chatchannels\cmds;

use chatchannels\ChannelSubscriber;
use chatchannels\ChatChannels;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\command\ConsoleCommandSender;
use pocketmine\command\PluginIdentifiableCommand;
use pocketmine\Player;

abstract class ChatChannelsCommand extends Command implements PluginIdentifiableCommand{
	/** @var ChatChannels */
	private $plugin;
	/** @var bool */
	private $testPerm;

	/**
	 * @param ChatChannels $plugin
	 * @param bool         $testPerm
	 * @param null|string  $name
	 * @param string       $desc
	 * @param null         $usage
	 * @param              $aliases
	 */
	public function __construct(ChatChannels $plugin, $testPerm = true, $name, $desc = "", $usage = null, ...$aliases){
		$this->plugin = $plugin;
		$this->testPerm = true;
		parent::__construct($name, $desc, $usage, $aliases);
	}

	/**
	 * @return ChatChannels
	 */
	public function getPlugin(){
		return $this->plugin;
	}

	public function execute(CommandSender $sender, $alias, array $args){
		/** @var ChannelSubscriber $sub */
		if($sender instanceof ConsoleCommandSender){
			$sub = $this->plugin->getConsole();
		}elseif($sender instanceof Player){
			$sub = $this->plugin->getPlayerSub($sender);
		}else{
			$class = new \ReflectionClass($sender);
			try{
				$method = $class->getMethod("getChannelSubscriber");
			}catch(\ReflectionException $e){
				$method = null;
			}
			if($method instanceof \ReflectionMethod){
				$sub = $method->invoke($sender);
			}
			if(!isset($sub) or !($sub instanceof ChannelSubscriber)){
				throw new \InvalidArgumentException("Unknown command sender");
			}
		}
		$r = $this->onRun($args, $sender, $sub);
		if(is_string($r)){
			$sender->sendMessage($r);
		}elseif($r === false){
			$sender->sendMessage($this->getUsage());
		}
		return true;
	}

	protected abstract function onRun(array $args, CommandSender $sender, ChannelSubscriber $sub);
}
