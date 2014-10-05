<?php

namespace pemapmodder\spicycap\provider;

interface DataProvider{
	public function dp_getMain();
	public function dp_getPoints($ip);
	public function dp_close();
}
