<?php

namespace chatchannels\cmds;

use chatchannels\Channel;
use chatchannels\ChannelSubscriber;
use pocketmine\command\CommandSender;

class JoinCommand extends ChatChannelsCommand{
	public function onRun(array $args, CommandSender $sender, ChannelSubscriber $sub){
		if(!isset($args[0])){
			return false;
		}
		$channel = $this->getPlugin()->getChannelManager()->getChannel($args[0]);
		if(!($channel instanceof Channel)){
			return "Channel $channel doesn't exist!";
		}
		if(!$sender->hasPermission($channel->getJoinPermission())){
			return "You don't have permission to join channel $channel";
		}
		$channel->join($sub);
		return "You joined channel $channel";
	}
}
