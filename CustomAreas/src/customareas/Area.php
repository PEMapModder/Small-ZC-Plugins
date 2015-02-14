<?php

namespace customareas;

use customareas\db\Database;
use customareas\shape\Shape;
use pocketmine\Server;

class Area{
	/** @var int */
	private $id;
	/** @var Shape */
	private $shape;
	/** @var int */
	private $flags;
	/** @var string */
	private $owner;
	/** @var bool */
	private $isAdd;
	private $valid;
	public function __construct($id, Shape $shape, $flags, $owner, $isAdd = false){
		$this->id = $id;
		$this->shape = $shape;
		$this->flags = $flags;
		$this->owner = $owner;
		$this->isAdd = $isAdd;
		$this->valid = !$isAdd; // if is new, it is not valid because it is not added into the database
	}
	public function init(Server $server){
		$this->shape->init($server);
	}
	public function getId(){
		return $this->id;
	}
	public function getShape(){
		return $this->shape;
	}
	public function getFlags(){
		return $this->flags;
	}
	public function setFlags($flags){
		$this->flags = $flags;
		$this->invalidate();
	}
	public function getOwner(){
		return $this->owner;
	}
	public function setOwner($owner){
		$this->owner = $owner;
		$this->invalidate();
	}
	public function invalidate(){
		$this->valid = false;
	}
	public function validate(Database $db){
		if($this->isAdd){
			$db->addArea($this);
		}
		else{
			$db->updateArea($this);
		}
		$this->valid = true;
	}
	public function getLevel(){
		return $this->getShape()->getLevel();
	}
}
