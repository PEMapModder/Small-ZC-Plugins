<?php

namespace chatchannels;

use pocketmine\permission\Permission;

class Channel{
	const LEVEL_IMPORTANT = 9;
	const LEVEL_NOTICE = 7;
	const LEVEL_CHAT = 5;
	const LEVEL_VERBOSE_INFO = 1;
	const MODE_FOUNDER = "founder";
	const MODE_ADMIN = "admin";
	const MODE_MOD = "moderator";
	const MODE_MUTED = "muted";
	const MODE_BANNED = "banned";
	/** @var string */
	private $name;
	/** @var Permission */
	private $joinPerm;
	/** @var string[][] */
	private $modes = [];
	/** @var ChannelSubscriber[] */
	private $subs = [];
	/**
	 * @param string $name
	 * @param Permission $joinPerm
	 * @param ChannelSubscriber $founder
	 */
	function __construct($name, Permission $joinPerm, ChannelSubscriber $founder){
		$this->name = $name;
		$this->joinPerm = $joinPerm;
		$this->modes[$founder->getID()] = [self::MODE_FOUNDER, self::MODE_ADMIN, self::MODE_MOD];
	}
	public function getJoinPermission(){
		return $this->joinPerm;
	}
	public function join(ChannelSubscriber $sub){
		if(isset($this->subs[$hash = spl_object_hash($sub)])){
			return "already joined"; // already joined
		}
		$flags = $this->getFlags($sub->getID());
		if(in_array(self::MODE_BANNED, $flags)){
			return "banned";
		}
		$this->subs[$hash] = $sub;
		return true;
	}
	public function quit(ChannelSubscriber $sub){
		if(!isset($this->subs[$hash = spl_object_hash($sub)])){
			return false; // is not on this channel
		}
		unset($this->subs[$hash]);
		return true;
	}
	public function send($message, ChannelSubscriber $source){
		if($source->isMuted()){
			$source->sendMessage("You are muted!");
		}
		if(in_array(self::MODE_MUTED, $this->getFlags($source->getID()))){
			$source->sendMessage("You are muted on this channel!");
		}
		$this->broadcast($message, self::LEVEL_CHAT);
	}
	public function broadcast($message, $level){
		foreach($this->subs as $sub){
			if($sub->getSubscribingLevel() <= $level){
				$sub->sendMessage($message);
			}
		}
	}
	public function getFlags($id){
		return isset($this->modes[$id]) ? $this->modes[$id]:[];
	}
}
