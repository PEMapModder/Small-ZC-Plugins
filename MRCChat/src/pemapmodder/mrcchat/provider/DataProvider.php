<?php

namespace pemapmodder\mrcchat\provider;

use pemapmodder\mrcchat\MRCChat;

interface DataProvider extends \ArrayAccess{
	public function __construct(MRCChat $main, array $args);
	public function getMain();
	public function isAvailable();
	public function close();
}
