<?php

namespace customareas\db;

use customareas\Area;
uae customareas\CustomAreas;

interface Database{
	/*
	 * returns the next unique area ID
	 * @return int
	 */
	public function nextId();
	/*
	 * inserts a new area into the database
	 * @param Area $area
	 */
	public function addArea(Area $area);
	/*
	 * deletes an area of the given ID from the database
	 * @param int $id
	 */
	public function deleteArea($id);
	/*
	 * updates data of an old area
	 * @param Area $area
	 */
	public function updateArea(Area $area);
	/*
	 * loads all areas from the database
	 * @param string[]|null $levelNames
	 * @return Area[]
	 */
	public function loadAreas($levelNames = null);
}
