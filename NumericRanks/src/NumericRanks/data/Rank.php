<?php

namespace NumericRanks\data;

use NumericRanks\NumericRanks;

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

class Rank
{
    /**
     * @param NumericRanks $plugin
     * @param $rankName
     */
	public function __construct(NumericRanks $plugin, $rankName)
	{
		$this->plugin = $plugin;
		$this->rankName = $rankName;
	}

    /**
     * @return mixed
     */
	public function getData()
	{
		return $this->plugin->getConfig()->getNested("ranks." . $this->rankName);
	}

    /**
     * @return mixed
     */
	public function getName()
	{
		return $this->rankName;
	}

    /**
     * @return array
     */
    public function getPermissions()
    {
        $perms = [];
        
        foreach($this->plugin->getRegisteredPermissions() as $perm => $index)
        {
            if($this->getRankIndex() >= $index)
            {
                $perms[$perm] = true;
            }
            else
            {
                $perms[$perm] = false;
            }
        }
        
        return $perms;
    }

    /**
     * @return mixed
     */
	public function getRankIndex()
	{
		return $this->getData()["index"];
	}

    /**
     * @return bool
     */
	public function isDefault()
	{
		$result = isset($this->getData()["defaultRank"]) and $this->getData()["defaultRank"] == true;
		
		return $result;
	}
}