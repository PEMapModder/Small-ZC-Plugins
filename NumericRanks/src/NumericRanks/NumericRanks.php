<?php

namespace NumericRanks;

use pocketmine\plugin\PluginBase;

use NumericRanks\data\Rank;

use pocketmine\utils\Config;

/*

	##    ## ##     ## ##     ## ######## ########  ####  ######  ########     ###    ##    ## ##    ##  ######  #### 
	###   ## ##     ## ###   ### ##       ##     ##  ##  ##    ## ##     ##   ## ##   ###   ## ##   ##  ##    ## #### 
	####  ## ##     ## #### #### ##       ##     ##  ##  ##       ##     ##  ##   ##  ####  ## ##  ##   ##       #### 
	## ## ## ##     ## ## ### ## ######   ########   ##  ##       ########  ##     ## ## ## ## #####     ######   ##  
	##  #### ##     ## ##     ## ##       ##   ##    ##  ##       ##   ##   ######### ##  #### ##  ##         ##      
	##   ### ##     ## ##     ## ##       ##    ##   ##  ##    ## ##    ##  ##     ## ##   ### ##   ##  ##    ## #### 
	##    ##  #######  ##     ## ######## ##     ## ####  ######  ##     ## ##     ## ##    ## ##    ##  ######  #### 

*/

class NumericRanks extends PluginBase
{
	public function onEnable()
	{
		$this->saveDefaultConfig();
	}
	
	/*
		   ###    ########  #### 
		  ## ##   ##     ##  ##  
		 ##   ##  ##     ##  ##  
		##     ## ########   ##  
		######### ##         ##  
		##     ## ##         ##  
		##     ## ##        #### 
	*/
	
	public function getRank($rankName)
	{
		$rank = new Rank($this, $rankName);
		
		if($rank->getData() == null) return null;
		
		return $rank;
	}
	
	public function getRanks()
	{
		$ranks = [];
		
		foreach(array_keys($this->getConfig()->get("ranks")) as $rankName)
		{
			array_push($ranks, new Rank($this, $rankName));
		}
		
		return $ranks;
	}
	
	public function reload()
	{
		$this->reloadConfig();
	}
}