<?php

namespace chatchannels;

class ConsoleSubscriber implements ChannelSubscriber{
	private $plugin;
	private $displayName;
	public $subLevel = Channel::LEVEL_CHAT;

	public function __construct(ChatChannels $plugin, $displayName){
		$this->plugin = $plugin;
		$this->displayName = $displayName;
	}

	public function getID(){
		return "server/console";
	}

	public function getDisplayName(){
		return $this->displayName;
	}

	public function sendMessage($message, Channel $channel){
		$this->plugin->getLogger()->info("<$channel> $message"); // should I use server logger instead?
	}

	public function getSubscribingLevel(){
		return $this->subLevel;
	}

	public function isMuted(){
		return false;
	}
}
