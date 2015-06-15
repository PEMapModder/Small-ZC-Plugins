<?php

namespace NumericRanks\provider;

use NumericRanks\NumericRanks;

use pocketmine\IPlayer;

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

class YamlProvider implements NumRanksProvider
{
    /**
     * @param NumericRanks $plugin
     */
    public function __construct(NumericRanks $plugin)
    {
        $this->plugin = $plugin;

        $this->init();
    }

    public function init()
    {
        @mkdir($this->plugin->getDataFolder() . "players/", 0777, true);
    }

    /**
     * @param IPlayer $player
     * @return Config
     */
    public function getPlayerConfig(IPlayer $player)
    {
        $fileName = $this->plugin->getDataFolder() . "players/" . strtolower($player->getName()) . ".yml";

        if(!(file_exists($fileName)))
        {
            return new Config($fileName, Config::YAML, [
                "name" => $player->getName(),
                "rank" => $this->plugin->getDefaultRank()->getName(),
            ]);
        }
        else
        {
            return new Config($fileName, Config::YAML, []);
        }
    }

    public function close()
    {
    }
}
