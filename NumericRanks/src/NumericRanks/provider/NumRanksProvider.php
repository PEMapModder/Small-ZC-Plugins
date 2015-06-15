<?php

namespace NumericRanks\provider;

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

interface NumRanksProvider
{
    public function init();

    public function getPlayerConfig(IPlayer $player);
    /**
     * @param IPlayer $player
     * @param string $rank
     */
    public function setPlayer(IPlayer $player, $rank);

    public function close();
}
