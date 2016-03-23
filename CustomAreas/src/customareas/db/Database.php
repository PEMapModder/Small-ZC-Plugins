<?php

namespace customareas\db;

use customareas\area\Area;
use pocketmine\level\Position;

abstract class Database{
	/**
	 * @param mixed|null $args
	 */
	public abstract function init($args);

	/**
	 * @return void
	 */
	public abstract function close();

	/**
	 * @return string
	 */
	public abstract function getName();

	/**
	 * @param Area $area
	 */
	public abstract function addArea(Area $area);

	/**
	 * @param Area $area
	 */
	public abstract function rmArea(Area $area);

	/**
	 * @param string $name
	 *
	 * @return Area
	 */
	public abstract function getArea($name);

	/**
	 * @return Area[]
	 */
	public abstract function getAreas();

	/**
	 * @param Position $pos
	 *
	 * @return Area|null
	 */
	public function searchAreaByPosition(Position $pos){
		foreach($this->getAreas() as $area){
			$l = $area->getShape()->getLevelName();
			if($l !== $pos->getLevel()->getName()){
				continue;
			}
			if($area->getShape()->isInside($pos)){
				return $area;
			}
		}
		return null;
	}
}
