<?php

namespace customareas\db;

use customareas\Area;
use customareas\CustomAreas;

interface Database{
	/**
	 * returns the {@link CustomAreas} object
	 * @return CustomAreas
	 */
	public function getPlugin();
	/**
	 * returns the next unique area ID
	 * @return int
	 */
	public function nextId();
	/**
	 * inserts a new area into the database
	 * @param Area $area
	 */
	public function addArea(Area $area);
	/**
	 * deletes an area of the given ID from the database
	 * @param int $id
	 */
	public function deleteArea($id);
	/**
	 * updates data of an old area
	 * @param Area $area
	 */
	public function updateArea(Area $area);
	/**
	 * loads all areas of the level from the database
	 * @param string|null $levelName
	 * @return Area[]
	 */
	public function loadAreas($levelName = null);
	/**
	 * finalize the database
	 */
	public function close();
}
