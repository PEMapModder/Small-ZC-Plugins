<?php

namespace chatchannels;

use pocketmine\plugin\Plugin;
use pocketmine\Server;

class PrefixAPI{
	private $plugin;
	/** @var PriorityPrefixEnum[] */
	private $prefixes = [];
	/** @var string[] */
	private $pluginsUsing = [];

	public function __construct(ChatChannels $plugin){
		$this->plugin = $plugin;
	}

	public static function addPrefix(Server $server, Plugin $context, Prefix $prefix, $priority){
		self::getInstance($server)->registerPrefix($context, $prefix, $priority);
	}

	public function registerPrefix(Plugin $context, Prefix $prefix, $priority){
		$this->pluginsUsing[spl_object_hash($context)] = true;
		if(!isset($this->prefixes[$priority])){
			$this->prefixes[$priority] = new PriorityPrefixEnum;
			krsort($this->prefixes, SORT_NUMERIC);
		}
		$this->prefixes[$priority]->add($prefix, $context);
	}

	public function recalculateAll(Plugin $context = null){
		if($context !== null){
			if(!isset($this->pluginsUsing[$k = spl_object_hash($context)])){
				return;
			}
			unset($this->pluginsUsing[$k]);
		}
		foreach($this->prefixes as $prefixes){
			$prefixes->recalculate();
		}
	}

	public function getPrefixes(ChannelSubscriber $sender, Channel $channel){
		$output = "";
		foreach($this->prefixes as $enum){
			$output .= $enum->getPrefixes($sender, $channel);
		}
		return $output;
	}

	public static function getInstance(Server $server){
		return ChatChannels::getInstance($server)->getPrefixAPI();
	}
}
