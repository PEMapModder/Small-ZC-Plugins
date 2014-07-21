<?php

namespace pemapmodder\minecartpro;

use pocketmine\metadata\MetadataValue;

class MinecartMetaValue extends MetadataValue{
	private $id;
	public function __construct(Main $main, $id){
		parent::__construct($main);
	}
	public function value(){
		return $this->id;
	}
	public function invalidate(){

	}
}
