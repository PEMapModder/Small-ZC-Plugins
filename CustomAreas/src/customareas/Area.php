<?php

namespace customareas;

use customareas\db\Database;
use customareas\shape\Shape;

class Area{
	/** @var int */
	private $id;
	/** @var Shape */
	private $shape;
	/** @var int */
	private $flags;
	/** @var string */
	private $owner;
	private $valid = true;
	public function __construct($id, Shape $shape, $flags, $owner){
		$this->id = $id;
		$this->shape = $shape;
		$this->flags = $flags;
		$this->owner = $owner;
	}
	public function getId(){
		return $this->id;
	}
	public function getShape(){
		return $this->shape;
	}
	public function invalidate(){
		$this->valid = false;
	}
	public function validate(Database $db){
		$db->updateArea($this);
		$this->valid = true;
	}
}
