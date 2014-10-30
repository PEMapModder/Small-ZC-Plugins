<?php

namespace chatchannels;

interface Prefix{
	public function getPrefix(ChannelSubscriber $sender, Channel $channel);
}
