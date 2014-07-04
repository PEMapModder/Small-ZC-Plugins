<?php

namespace pemapmodder\worldeditart\utils\subcommand;

use pemapmodder\worldeditart\utils\spaces\Space;

class Cut extends Copy{
	public function getName(){
		return "cut";
	}
	public function getDescription(){
		return "Same as /wea copy [...] and then /wea set air";
	}
	public function getPermissionRoot(){
		return "wea.clipboard.cut.";
	}
	public function onPostRun(array $blocks, Space $selection){
		$selection->clear();
		parent::onPostRun($blocks);
	}
}
