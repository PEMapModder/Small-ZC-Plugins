<?php

/*
 * PlotIt
 *
 * Copyright (C) 2015 PEMapModder and contributors
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * @author PEMapModder
 */

namespace plotit;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\plugin\PluginBase;

class PlotIt extends PluginBase{
	public function onCommand(CommandSender $issuer, Command $cmd, $l, array $params){
		if(count($params) === 0){
			$this->sendUsage($issuer, $l);
			return true;
		}
		return true;
	}
	private function sendUsage(CommandSender $issuer, $l){
		$issuer->sendMessage("Usage: /$l <function in x> ; <particle> [data] <x> <y> <z> <from x> <from y> <to x> <to y> <scale>");

	}
}
