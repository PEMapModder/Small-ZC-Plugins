<?php

namespace NumericRanks;

use pocketmine\event\Listener;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerKickEvent;
use pocketmine\event\player\PlayerQuitEvent;

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

class PlayerListener implements Listener
{
    public function __construct(NumericRanks $plugin)
    {
        $this->plugin = $plugin;
    }
    
    public function onJoin(PlayerJoinEvent $event)
    {
        $player = $event->getPlayer();
        
        $this->plugin->setPermissions($player);
    }
    
    public function onKick(PlayerKickEvent $event)
    {
        $player = $event->getPlayer();

        $this->plugin->removeAttachment($player);
    }
    
    public function onQuit(PlayerQuitEvent $event)
    {
        $player = $event->getPlayer();
        
        $this->plugin->removeAttachment($player);
    }
}