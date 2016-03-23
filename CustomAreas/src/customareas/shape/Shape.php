<?php

namespace customareas\shape;

use pocketmine\math\Vector3;

interface Shape extends \Serializable{
	/**
	 * @return string
	 */
	public static function getName();

	/**
	 * @param Vector3 $p
	 *
	 * @return bool
	 */
	public function isInside(Vector3 $p);

	/**
	 * @return string
	 */
	public function getLevelName();
}
