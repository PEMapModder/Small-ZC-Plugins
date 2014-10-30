<?php

namespace chatchannels;

interface ChannelSubscriber{
	/**
	 * @return string
	 */
	public function getID();
	/**
	 * @return string
	 */
	public function getDisplayName();
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
	 * @param Channel $channel
	 */
	public function sendMessage($message, Channel $channel);
}
