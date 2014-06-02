<?php

namespace pemapmodder\vehicles;

class VehicleScale{
	protected $args;
	/**
	 * @param int $xp
	 * @param int $zp
	 * @param int $xm
	 * @param int $zm
	 * @param int $yp
	 * @param int $ym
	 */
	public function __construct($xp, $zp, $xm, $zm, $yp, $ym){
		$this->args = array_slice(func_get_args(), 0, 6);
	}
	public function get($k){
		switch($k){
			case "x+":
				return $this->args[0];
			case "z+":
				return $this->args[1];
			case "x-":
				return $this->args[2];
			case "z-":
				return $this->args[3];
			case "y+":
				return $this->args[4];
			case "y-":
				return $this->args[5];
		}
		trigger_error("IllegalArgument passed", E_USER_WARNING);
		return false;
	}
}