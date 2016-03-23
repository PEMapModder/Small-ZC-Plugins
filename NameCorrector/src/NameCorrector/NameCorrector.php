<?php

/**
 * NameCorrector
 * Copyright (C) 2015 PEMapModder
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License along
 * with this program; if not, write to the Free Software Foundation, Inc.,
 * 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301 USA.
 */

namespace NameCorrector;

use pocketmine\event\Listener;
use pocketmine\event\server\DataPacketReceiveEvent;
use pocketmine\network\protocol\Info as ProtocolInfo;
use pocketmine\plugin\PluginBase;

class NameCorrector extends PluginBase implements Listener{
	/** @var Special[] */
	private $specials;
	/** @var string */
	private $default;
	/** @var string */
	private $padding;
	/** @var int */
	private $min, $max;
	/** @var bool */
	private $multibyte;

	public function onEnable(){
		$this->saveDefaultConfig();
		/** @var string[]|string[][] $special */
		foreach($this->getConfig()->get("specials", []) as $i => $special){
			if(!isset($special["from"])){
				$this->getLogger()->warning("The " . self::num_addOrdinal($i + 1) . " special replace in config.yml has an error - missing \"from\" property!");
				continue;
			}
			if(!isset($special["to"])){
				$this->getLogger()->warning("The " . self::num_addOrdinal($i + 1) . " special replace in config.yml has an error - missing \"to\" property!");
				continue;
			}
			if(strlen($special["to"]) > 1){
				$this->getLogger()->warning("The " . self::num_addOrdinal($i + 1) . " special replace in config.yml has an error - \"to\" property is too long!");
				continue;
			}
			$object = new Special;
			$object->from = is_array($special["from"]) ? $special["from"] : [$special["from"]];
			$object->to = $special["to"];
			$this->specials[] = $object;
		}
		$this->default = $this->getConfig()->get("default", "_");
		if(strlen($this->default) > 1){
			$this->getLogger()->warning("The \"default\" property in config.yml has an error - the value is too long! Assuming as \"_\".");
			$this->default = "_";
		}
		$this->padding = $this->getConfig()->get("padding", "_");
		$this->min = $this->getConfig()->get("min", 3);
		$this->max = $this->getConfig()->get("truncate", 16);
		if($this->max === -1 or $this->max === "-1"){
			$this->max = PHP_INT_MAX;
		}
		$this->multibyte = function_exists("mb_substr") and function_exists("mb_strlen");
		if(!$this->multibyte){
			$this->getLogger()->notice("The PHP extension \"multibyte\" is not available on this server. Multi-byte characters in player names may get replaced into multiple, instead of single, underscores.");
		}
		$this->getServer()->getPluginManager()->registerEvents($this, $this);
	}

	/**
	 * @param DataPacketReceiveEvent $event
	 *
	 * @priority LOW
	 */
	public function onReceivePacket(DataPacketReceiveEvent $event){
		$pk = $event->getPacket();
		if($pk->pid() === ProtocolInfo::LOGIN_PACKET){
			/** @var \pocketmine\network\protocol\LoginPacket $pk */
			$pk->username = $this->correctName($pk->username);
//			var_dump($pk->username);
		}
	}

	public function correctName($name){
		foreach($this->specials as $special){
			$name = str_replace($special->from, $special->to, $name);
		}
		if($this->multibyte and mb_strlen($name) !== strlen($name)){
			$length = mb_strlen($name, "UTF-8");
			$new = "";
			for($i = 0; $i < $length; $i++){
				$char = mb_substr($name, $i, 1, "UTF-8");
				if(strlen($char) > 1){
					$char = $this->default;
				}
				$new .= $char;
			}
			$name = $new;
		}
		$name = preg_replace('/[^A-Za-z0-9_]/', $this->default, $name);
		$name = substr($name, 0, min($this->max, strlen($name)));
		if($this->padding !== ""){
			while(strlen($name) < $this->min){
				$name .= $this->padding;
			}
		}
		return $name;
	}

	public static function num_addOrdinal($num){
		return $num . self::num_getOrdinal($num);
	}

	public static function num_getOrdinal($num){
		$rounded = $num % 100;
		if(3 < $rounded and $rounded < 21){
			return "th";
		}
		$unit = $rounded % 10;
		if($unit === 1){
			return "st";
		}
		if($unit === 2){
			return "nd";
		}
		return $unit === 3 ? "rd" : "th";
	}
}
