<?php

namespace pemapmodder\worldeditart\utils\provider;

interface Cached{
	public function collectGarbage($expiry);
}
