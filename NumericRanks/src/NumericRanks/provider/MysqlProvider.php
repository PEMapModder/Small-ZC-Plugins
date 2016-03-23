<?php

namespace NumericRanks\provider;

use mysqli;
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

class MysqlProvider implements NumRanksProvider{
	/** @var mysqli */
	private $db;

	/**
	 * @param NumericRanks $plugin
	 */
	public function __construct(NumericRanks $plugin){
		$this->plugin = $plugin;

		$this->init();
	}

	public function init(){
		$config = $this->plugin->getConfig();
		$host = $config->getNested("dataProvider.mysql.host", "127.0.0.1");
		$user = $config->getNested("dataProvider.mysql.username", "root");
		$pw = $config->getNested("dataProvider.mysql.password", "");
		$db = $config->getNested("dataProvider.mysql.database", "numranks");
		$port = $config->getNested("dataProvider.mysql.port", 3306);
		$this->db = new mysqli($host, $user, $pw, $db, $port);
		$this->db->query("CREATE TABLE IF NOT EXISTS numranks (name VARCHAR(32) PRIMARY KEY, rank VARCHAR(255))");
	}

	/**
	 * @param IPlayer $player
	 *
	 * @return array
	 */
	public function getPlayerConfig(IPlayer $player){
		// TODO
	}

	public function setPlayer(IPlayer $player, $rank){
		// TODO
	}

	public function close(){
		$this->db->close();
	}
}
