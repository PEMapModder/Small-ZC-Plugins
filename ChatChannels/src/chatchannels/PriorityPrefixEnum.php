<?php

namespace chatchannels;

use pocketmine\plugin\Plugin;

class PriorityPrefixEnum{
	/** @var array[] */
	private $registries = [];
	public function add(Prefix $prefix, Plugin $context){
		$this->registries[] = ["prefix" => $prefix, "plugin" => $context];
	}
	public function recalculate(){
		foreach($this->registries as $k => $reg){
			/** @var Plugin $plugin */
			$plugin = $reg["plugin"];
			if(!$plugin->isEnabled()){
				unset($this->registries[$k]);
			}
		}
		$this->registries = array_values($this->registries);
	}
	public function getPrefixes(ChannelSubscriber $sender, Channel $channel, $recalculate = false){
		if($recalculate){
			$this->recalculate();
		}
		$output = "";
		foreach($this->registries as $reg){
			/** @var Prefix $prefix */
			$prefix = $reg["prefix"];
			$output .= $prefix->getPrefix($sender, $channel);
		}
		return $output;
	}
}
