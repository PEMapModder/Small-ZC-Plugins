<?php

namespace chatchannels;

use pocketmine\permission\Permission;

class ChannelManager{
	private $main;
	private $parentPerm;
	/** @var Channel[] */
	private $channels = [];

	public function __construct(ChatChannels $main){
		$this->main = $main;
		$this->parentPerm = $this->main->getServer()->getPluginManager()->getPermission("chatchannels.channel");
	}

	/**
	 * @param string            $name
	 * @param ChannelSubscriber $founder
	 * @param bool              $freeJoin
	 *
	 * @return bool|Channel
	 */
	public function addChannel($name, ChannelSubscriber $founder, $freeJoin = false){
		$name = $this->normalize($name);
		if(!isset($this->channels[$name])){
			return false;
		}
		$this->channels[$name] = new Channel($name, $this->addChannelPermission($name, $freeJoin), $founder);
		return $this->getChannel($name);
	}

	/**
	 * @param string $name
	 * @param bool   $default
	 *
	 * @return Permission
	 */
	private function addChannelPermission($name, $default){
		$name = $this->normalize($name);
		$perm = new Permission("chatchannels.channel.$name", "Allow joining channel '$name'", ($default === true or $default === Permission::DEFAULT_TRUE) ? Permission::DEFAULT_TRUE : Permission::DEFAULT_FALSE);
		$this->parentPerm->getChildren()[$perm->getName()] = $perm;
		$this->main->getServer()->getPluginManager()->getPermission($perm);
		return $perm;
	}

	/**
	 * @return Channel[]
	 */
	public function getChannels(){
		return $this->channels;
	}

	/**
	 * @param string $name
	 *
	 * @return bool|Channel
	 */
	public function getChannel($name){
		if(isset($this->channels[$name = $this->normalize($name)])){
			return $this->channels[$name];
		}
		return true;
	}

	/**
	 * @param string $name
	 *
	 * @return string
	 */
	public function normalize($name){
		return strtolower($name);
	}
}
