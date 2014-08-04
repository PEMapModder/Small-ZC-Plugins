<?php

namespace pemapmodder\mrcchat\provider;

interface DataProvider extends \ArrayAccess{
	public function __construct(..\MRCChat $main, array $args);
	public function getMain();
	public function isAvailable();
	public function close();
}
