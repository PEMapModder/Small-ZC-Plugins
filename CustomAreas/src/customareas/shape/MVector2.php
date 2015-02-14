<?php

namespace customareas\shape;

class MVector2 implements \Serializable{
	/** @var int */
	private $x, $z;
	public function serialize(){
		return serialize($this->x) . "|" . serialize($this->z);
	}
	public function unserialize($serialized){
		list($this->x, $this->z) = array_map("unserialize", explode("|", $serialized));
	}
	public function equals(MVector2 $other){
		return ($other->x === $this->x) and ($this->z === $other->z);
	}
	public function getX(){
		return $this->x;
	}
	public function getZ(){
		return $this->z;
	}
}
