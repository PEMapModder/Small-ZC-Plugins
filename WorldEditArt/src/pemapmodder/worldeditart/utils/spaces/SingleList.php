<?php

namespace pemapmodder\worldeditart\utils\spaces;

use pocketmine\block\Block;

class SingleList extends BlockList{
	protected $blocks;
	public function __construct($blocks){
		if(!is_array($blocks)){
			$blocks = [$blocks];
		}
		foreach($blocks as $block){
			if(!($block instanceof Block)){
				throw new \InvalidArgumentException("Argument 1 must be instance of Block or an array of Block, an element given is ".var_export($block, true), E_USER_ERROR);
			}
		}
		$this->blocks = $blocks;
	}
	public function getRandom(){
		return array_rand($this->blocks);
	}
}
