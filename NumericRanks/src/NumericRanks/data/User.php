<?php

namespace NumericRanks\data;

use NumericRanks\NumericRanks;

use pocketmine\IPlayer;

/*
    
    NumericRanks v1.0.0 by PEMapModder & 64FF00 :3

    ##    ## ##     ## ##     ## ######## ########  ####  ######  ########     ###    ##    ## ##    ##  ######  #### 
    ###   ## ##     ## ###   ### ##       ##     ##  ##  ##    ## ##     ##   ## ##   ###   ## ##   ##  ##    ## #### 
    ####  ## ##     ## #### #### ##       ##     ##  ##  ##       ##     ##  ##   ##  ####  ## ##  ##   ##       #### 
    ## ## ## ##     ## ## ### ## ######   ########   ##  ##       ########  ##     ## ## ## ## #####     ######   ##  
    ##  #### ##     ## ##     ## ##       ##   ##    ##  ##       ##   ##   ######### ##  #### ##  ##         ##      
    ##   ### ##     ## ##     ## ##       ##    ##   ##  ##    ## ##    ##  ##     ## ##   ### ##   ##  ##    ## #### 
    ##    ##  #######  ##     ## ######## ##     ## ####  ######  ##     ## ##     ## ##    ## ##    ##  ######  #### 

*/

class User
{
	public function __construct(NumericRanks $plugin, IPlayer $player)
	{
        $this->player = $player;
        $this->plugin = $plugin;
	}
    
    public function getConfig()
    {
        return $this->plugin->getProvider()->getPlayerConfig($this->player);
    }
    
    public function getPermissions()
    {
        return $this->getRank()->getPermissions();
    }
    
    public function getPlayer()
    {
        return $this->player;
    }
    
    public function getRank()
    {
        $rankName = $this->getConfig()->getNested("rank");
        $rank = $this->plugin->getRank($rankName);
        
        return $rank;
    }
}