<?php

namespace customareas\shape;

use pocketmine\level\Position;
use pocketmine\Server;

interface Shape extends \Serializable{
	public function isInside(Position $pos);
	public function init(Server $server);
	/**
	 * @return \pocketmine\level\Level
	 */
	public function getLevel();
}
