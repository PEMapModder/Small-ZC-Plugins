<?php

namespace pemapmodder\spicycap;

class BanRule{
	/** @var int */
	private $min, $max, $secs;
	public function __construct(array $config){
		$this->min = isset($config["at least"]) ? $config["at least"]:0;
		$this->max = isset($config["at most"]) ? $config["at most"]:PHP_INT_MAX;
		$this->secs = $config["ban period"] * 3600; // hours -> seconds
	}
	public function getSeconds($points){
		if($this->min <= $points and $this->max >= $points){
			return $this->secs;
		}
		return 0;
	}
}
