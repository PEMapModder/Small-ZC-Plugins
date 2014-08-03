<?php

namespace pemapmodder\worldeditart\utils\provider;

interface DataProvider extends \ArrayAccess{
	/**
	 * @return string
	 */
	public function getName();
	/**
	 * @return bool
	 */
	public function isAvailable();
	public function close();
}
