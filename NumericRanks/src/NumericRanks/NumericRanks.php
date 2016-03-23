<?php

namespace NumericRanks;

use NumericRanks\data\Rank;
use NumericRanks\data\User;
use NumericRanks\provider\MysqlProvider;
use NumericRanks\provider\NumRanksProvider;
use NumericRanks\provider\YamlProvider;
use pocketmine\IPlayer;
use pocketmine\permission\Permission;
use pocketmine\Player;
use pocketmine\plugin\PluginBase;
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

class NumericRanks extends PluginBase{
	/** @var array */
	private $attachments = [];

	/** @var NumRanksProvider */
	private $provider;

	/** @var Config */
	private $permsConfig;

	public function onEnable(){
		$this->saveDefaultConfig();

		$this->permsConfig = new Config($this->getDataFolder() . "perms.yml", Config::YAML);

		$dataProvider = $this->getConfig()->getNested("dataProvider.name", "yaml");
		switch($dataProvider){
			case "mysql":
				$this->getLogger()->notice("Enabling MySQL provider...");
				try{
					$this->provider = new MysqlProvider($this);
					break;
				}catch(\Exception $e){
					$this->getLogger()->critical("Could not connect to MySQL server: " . $e->getMessage());
					$this->getLogger()->notice("Changing to YAML provider");
				}
			case "yaml":
			case "yml":
				$this->getLogger()->notice("Enabling YAML provider...");
				$this->provider = new YamlProvider($this);
				break;
		}

		$this->updatePerms();

		$this->getServer()->getPluginManager()->registerEvents(new PlayerListener($this), $this);

		$this->getLogger()->info("Enabled " . $this->getDescription()->getFullName() . " with " . (new \ReflectionClass($this->provider))->getShortName() . " as data provider.");
	}

	public function onDisable(){
		if($this->provider instanceof NumRanksProvider){
			$this->provider->close();
		}
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

	/**
	 * @param Player $player
	 *
	 * @return mixed
	 */
	public function getAttachment(Player $player){
		if(!isset($this->attachments[$player->getName()])){
			$this->attachments[$player->getName()] = $player->addAttachment($this);
		}

		return $this->attachments[$player->getName()];
	}

	/**
	 * @return mixed
	 */
	public function getDefaultRank(){
		$defaultRanks = [];

		foreach($this->getRanks() as $rank){
			if($rank->isDefault()){
				array_push($defaultRanks, $rank);
			}
		}

		switch(count($defaultRanks)){
			case 1:

				$defaultRank = $defaultRanks[0];

				break;

			default:

				if(count($defaultRanks) > 1){
					throw new \RuntimeException("More than one default rank was declared in config.yml");
				}else{
					throw new \RuntimeException("No default group was found in config.yml");
				}

				break;
		}

		return $defaultRank;
	}

	/**
	 * @return mixed
	 */
	public function getProvider(){
		return $this->provider;
	}

	/**
	 * @param $rankName
	 *
	 * @return Rank
	 */
	public function getRank($rankName){
		$rank = new Rank($this, $rankName);

		if($rank->getData() == null){
			throw new \RuntimeException("Rank $rankName does NOT exist");
		}

		return $rank;
	}

	/**
	 * @return Rank[]
	 */
	public function getRanks(){
		$ranks = [];

		foreach(array_keys($this->getConfig()->getNested("ranks")) as $rankName){
			$ranks[] = new Rank($this, $rankName);
		}

		return $ranks;
	}

	/**
	 * @return mixed
	 */
	public function getRegisteredPermissions(){
		return $this->permsConfig->getAll();
	}

	/**
	 * @param IPlayer $player
	 *
	 * @return User
	 */
	public function getUser(IPlayer $player){
		return new User($this, $player);
	}

	public function reload(){
		$this->reloadConfig();
	}

	/**
	 * @param Player $player
	 */
	public function removeAttachment(Player $player){
		$attachment = $this->getAttachment($player);

		$player->removeAttachment($attachment);

		unset($this->attachments[$player->getName()]);
	}

	public function removeAttachments(){
		$this->attachments = [];
	}

	/**
	 * @param Player $player
	 */
	public function setPermissions(Player $player){
		$attachment = $this->getAttachment($player);

		$attachment->clearPermissions();

		$perms = $this->getUser($player)->getPermissions();

		ksort($perms);

		$attachment->setPermissions($perms);
	}

	public function updatePerms(){
		foreach($this->getServer()->getPluginManager()->getPermissions() as $perm){
			if(!$this->permsConfig->exists($perm->getName(), true)){
				$this->permsConfig->set(strtolower($perm->getName()), $this->getDefaultIndex($perm->getDefault()));
			}
		}

		$all = $this->permsConfig->getAll();
		asort($all, SORT_FLAG_CASE | SORT_NATURAL);
		$this->permsConfig->setAll($all);
	}

	private function getDefaultIndex($default){
		switch($default){
			case Permission::DEFAULT_TRUE:
				return $this->getConfig()->getNested("defaultPermissionIndex.true", 0);
			case Permission::DEFAULT_NOT_OP:
				return $this->getConfig()->getNested("defaultPermissionIndex.true", 0);
			case Permission::DEFAULT_OP:
				return $this->getConfig()->getNested("defaultPermissionIndex.true", 150);
			case Permission::DEFAULT_FALSE:
				return $this->getConfig()->getNested("defaultPermissionIndex.false", 999);
		}

		throw new \UnexpectedValueException("Unexpected permission default: $default");
	}
}
