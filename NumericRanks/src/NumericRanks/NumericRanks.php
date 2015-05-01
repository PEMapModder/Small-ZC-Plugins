<?php

namespace NumericRanks;

use pocketmine\Player;

use pocketmine\plugin\PluginBase;

use NumericRanks\data\Rank;

use pocketmine\utils\Config;

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
    
    // From PurePerms
    public function getAttachment(Player $player)
    {
        if(!isset($this->attachments[$player->getName()]))
        {
            $this->attachments[$player->getName()] = $player->addAttachment($this);
        }
        
        return $this->attachments[$player->getName()];
    }
    
    public function getDefaultRank()
    {
        $defaultRanks = [];
        
        foreach($this->getRanks() as $rank)
        {
            if($rank->isDefault()) array_push($defaultRanks, $rank);
        }
        
        // Checks whether two or more default ranks are set
        switch(count($defaultRanks))
        {
            case 1:
                 
                $defaultRank = $defaultRanks[0];
                
                break;
            
            default:
                
                if(count($defaultRanks) > 1))
                {
                    throw new \RuntimeException("More than one default rank was declared in config.yml");
                }
                else
                {
                    throw new \RuntimeException("No default group was found in config.yml");
                }
                
                break;
        }
        
        return $defaultRank;
    }
    
    public function getRank($rankName)
    {
        $rank = new Rank($this, $rankName);
        
        if($rank->getData() == null) throw new \RuntimeException("Rank $rankName does NOT exist");
        
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
    
    // From PurePerms
    public function removeAttachment(Player $player)
    {
        $attachment = $this->getAttachment($player);
        
        $player->removeAttachment($attachment);
        
        unset($this->attachments[$player->getName()]);
    }
    
    public function removeAttachments()
    {
        foreach($this->attachments as $attachment)
        {
            unset($this->attachments[$attachment]);
        }
    }
}