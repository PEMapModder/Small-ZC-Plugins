<?php

namespace chatchannels;

interface ChannelSubscriber{
	/**
	 * @return string
	 */
	public function getID();
	/**
	 * @return int
	 */
	public function getSubscribingLevel();
	/**
	 * @return bool
	 */
	public function isMuted();
	/**
	 * @param string $message
	 */
	public function sendMessage($message);
}
