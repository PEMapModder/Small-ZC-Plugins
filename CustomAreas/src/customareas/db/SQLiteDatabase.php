<?php

namespace customareas\db;

use customareas\Area;
use customareas\CustomAreas;

class SQLiteDatabase implements Database{
	/** @var CustomAreas */
	private $plugin;
	/** @var \SQLite3 */
	private $db;
	public function __construct(CustomAreas $plugin, $file){
		$this->plugin = $plugin;
		$this->db = new \SQLite3($file);
	}
	/**
	 * returns the {@link CustomAreas} object
	 * @return CustomAreas
	 */
	public function getPlugin(){
		// TODO: Implement getPlugin() method.
	}
	/**
	 * returns the next unique area ID
	 * @return int
	 */
	public function nextId(){
		// TODO: Implement nextId() method.
	}
	/**
	 * inserts a new area into the database
	 * @param Area $area
	 */
	public function addArea(Area $area){
		// TODO: Implement addArea() method.
	}
	/**
	 * deletes an area of the given ID from the database
	 * @param int $id
	 */
	public function deleteArea($id){
		// TODO: Implement deleteArea() method.
	}
	/**
	 * updates data of an old area
	 * @param Area $area
	 */
	public function updateArea(Area $area){
		// TODO: Implement updateArea() method.
	}
	/**
	 * loads all areas from the database
	 * @param string[]|null $levelName
	 * @return Area[]
	 */
	public function loadAreas($levelName = null){
		// TODO: Implement loadAreas() method.
	}
	/**
	 * finalize the database
	 */
	public function close(){
		// TODO: Implement close() method.
	}
}
