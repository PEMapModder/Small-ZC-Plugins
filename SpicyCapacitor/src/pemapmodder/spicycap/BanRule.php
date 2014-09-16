<?php

namespace pemapmodder\spicycap;

class BanRule{
	private $least = 0;
	private $most = PHP_INT_MAX;
	private $hours;
	public function __construct($array){
		if(isset($array["at least"])){
			$this->least = $array["at least"];
		}
		if(isset($array["at most"])){
			$this->most = $array["at most"];
		}
		$this->hours = $array["ban period"];
	}
	public function getHours($points){
		if($this->least <= $points and ($this->most === PHP_INT_MAX or $this->most >= $points)){
			return $this->hours;
		}
		return 0;
	}
}
