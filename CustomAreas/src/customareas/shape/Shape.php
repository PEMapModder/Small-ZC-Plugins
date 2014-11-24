<?php

namespace customareas\shape;

use pocketmine\level\Position;

interface Shape{
	public function __construct($data);
	public function isInside(Position $pos);
	public function export();
}
