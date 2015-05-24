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
    /**
     * @param NumericRanks $plugin
     * @param IPlayer $player
     */
	public function __construct(NumericRanks $plugin, IPlayer $player)
	{
        $this->player = $player;
        $this->plugin = $plugin;
	}

    /**
     * @return mixed
     */
    public function getConfig()
    {
        return $this->plugin->getProvider()->getPlayerConfig($this->player);
    }

    /**
     * @return array
     */
    public function getPermissions()
    {
        return $this->getRank()->getPermissions();
    }

    /**
     * @return IPlayer
     */
    public function getPlayer()
    {
        return $this->player;
    }

    /**
     * @return Rank
     */
    public function getRank()
    {
        $rankName = $this->getConfig()->getNested("rank");
        $rank = $this->plugin->getRank($rankName);
        
        return $rank;
    }
}