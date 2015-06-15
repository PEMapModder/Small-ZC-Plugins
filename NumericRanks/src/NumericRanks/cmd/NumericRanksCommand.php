<?php

namespace NumericRanks\cmd;

use NumericRanks\NumericRanks;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\command\PluginIdentifiableCommand;

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

class NumericRanksCommand extends Command implements PluginIdentifiableCommand{
    /** @var NumericRanks */
    private $main;

    public function __construct(NumericRanks $main)
    {
        $this->main = $main;
        parent::__construct("numranks", "NumericRanks main command", "/nr help", [
            "nr", "nrk", // main
            "nrpl", "nrplyr", // player
            "nrpm", "nrperm", // perm
            "nrrk", "nrrank", // rank
            "nradd", // addrank
            "nrrm", "nrremove", "nrdel", "nrdelete", // rmrank
        ]);
    }

    /**
     * @return NumericRanks
     */
    public function getPlugin()
    {
        return $this->main;
    }

    public function execute(CommandSender $sender, $alias, array $args)
    {
        switch($alias){
            case "nrpl":
            case "nrplyr":
                $arg = "player";
                break;
            case "nrpm":
            case "nrperm":
                $arg = "perm";
                break;
            case "nrrk":
            case "nrrank":
                $arg = "rank";
                break;
            case "nradd":
                $arg = "addrank";
                break;
            case "nrrm":
            case "nrremove":
            case "nrdel":
            case "nrdelete":
                $arg = "rmrank";
                break;
        }
        if(!isset($arg)){
            $arg = array_shift($args);
        }
        switch($arg)
        {
            case "player":
                $this->player($sender, $args);
                return true;
            case "perm":
                $this->perm($sender, $args);
                return true;
            case "addrank":
                $this->addrank($sender, $args);
                return true;
            case "rmrank":
                $this->rmrank($sender, $args);
                return true;
            case "rank":
                $this->rank($sender, $args);
                return true;
            default:
                $this->help($sender);
                return true;
        }
    }
    /**
     * @param CommandSender $sender
     * @param string[] $args
     */
    private function player(CommandSender $sender, array $args)
    {

    }
    /**
     * @param CommandSender $sender
     * @param string[] $args
     */
    private function perm(CommandSender $sender, array $args)
    {

    }
    /**
     * @param CommandSender $sender
     * @param string[] $args
     */
    private function addrank(CommandSender $sender, array $args)
    {

    }
    /**
     * @param CommandSender $sender
     * @param string[] $args
     */
    private function rmrank(CommandSender $sender, array $args)
    {

    }
    /**
     * @param CommandSender $sender
     * @param string[] $args
     */
    private function rank(CommandSender $sender, array $args)
    {

    }
    /**
     * @param CommandSender $sender
     */
    private function help(CommandSender $sender)
    {
        $sender->sendMessage("NumericRanks command: /numranks (aliases: /nr, /nrk)");
        $sender->sendMessage("/nr player <player>:  View player's rank and permission index");
        $sender->sendMessage("/nr player <player> <rank>: Set player's rank");
        $sender->sendMessage("/nr perm <permission>: View permission's required index and ranks that can use it");
        $sender->sendMessage("/nr perm <permission> <index>: Set permission's required index");
        $sender->sendMessage("/nr rank: List all ranks");
        $sender->sendMessage("/nr rank <rank>: View information about the rank");
        $sender->sendMessage("/nr addrank <rank>: Add a rank");
        $sender->sendMessage("/nr rmrank <rank> <backup>: Remove the rank <rank> and set the players with that rank to the <backup> rank");
    }

    // TODO page text
}
