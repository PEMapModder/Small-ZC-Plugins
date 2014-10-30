<?php

namespace chatchannels;

class CallbackPrefix implements Prefix{
	/** @var callable */
	private $callback;
	public function __construct(callable $callback){
		$this->callback = $callback;
	}
	public function getPrefix(ChannelSubscriber $sender, Channel $channel){
		return call_user_func($this->callback, $sender, $channel);
	}
}
