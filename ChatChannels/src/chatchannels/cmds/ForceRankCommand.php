<?php

namespace chatchannels\cmds;

use chatchannels\Channel;
use chatchannels\ChannelSubscriber;
use chatchannels\ChatChannels;
use pocketmine\command\CommandSender;

class ForceRankCommand extends ChatChannelsCommand{
	/** @var string */
	private $rankName, $flag;

	public function __construct(ChatChannels $plugin, $name, $flag, ...$aliases){
		parent::__construct($plugin, true, "force$name", "Make yourself a channel $name", "/force$name <channel>", ...$aliases);
		$this->setPermission("chatchannels.force$name");
		$this->rankName = $name;
		$this->flag = $flag;
	}

	public function onRun(array $args, CommandSender $sender, ChannelSubscriber $sub){
		if(!isset($args[0])){
			return false;
		}
		$channel = $this->getPlugin()->getChannelManager()->getChannel(array_shift($args));
		if(!($channel instanceof Channel)){
			return "Channel doesn't exist!";
		}
		$flags = $channel->getFlags($sub->getID());
		if(in_array($this->flag, $flags)){
			return "You are already {$this->rankName} on channel $channel!";
		}
		$flags[] = $this->flag;
		$channel->setFlags($sub->getID(), $flags);
		return "You are now a {$this->rankName} on channel $channel.";
	}
}
