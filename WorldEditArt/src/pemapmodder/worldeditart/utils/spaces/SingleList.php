<?php

namespace pemapmodder\worldeditart\utils\spaces;

class SingleList extends BlockList{
	private $blocks;
	public function __construct($blocks){
		$this->blocks = (array) $blocks;
	}
	public function getRandom(){
		return array_rand($this->blocks);
	}
}
