<?php

namespace pemapmodder\worldeditart\utils\spaces;

use pocketmine\block\Block;

class SingleList extends BlockList{
	protected $block;
	public function __construct(Block $block){
		$this->block = $block;
	}
	public function getRandom(){
		return $this->block;
	}
}
