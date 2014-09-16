<?php

namespace pemapmodder\spicycap;

use pemapmodder\spicycap\provider\DataProvider;
use pocketmine\command\defaults\BanCommand;
use pocketmine\command\defaults\BanIpCommand;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerPreLoginEvent;
use pocketmine\plugin\PluginBase;

class SpicyCap extends PluginBase implements Listener, DataProvider{
	// main class - fields
	/** @var DataProvider */
	private $provider;
	/** @var BanRule[] */
	private $banRules = [];
	/** @var string */
	private $banReason;
	private $actionLogger;
	// plugin base
	public function onEnable(){
		$this->saveDefaultConfig();
		$dbInfo = $this->getConfig()->get("database");
		$type = strtolower($dbInfo["type"]);
		switch($type){
			case "sqlite3":
				// TODO instantiate SQLite3 data provider
				break;
			case "mysql":
			case "mysqli":
				// TODO instantiate MySQLi data provider
				if("success"){ // TODO make provider if no error
					break;
				}
				else{
					$this->getLogger()->warning("Failed connecting to MySQL database!");
					$sup = true;
				}
			default:
				if(isset($sup) and $sup){
					$this->getLogger()->warning("Unknown data provider type '$type'; " .
						"Random Access Memory data provider will be used.");
				}
				$provider = $this;
				$provider->dp_construct();
				$this->provider = $this;
				break;
		}
		$banInfo = $this->getConfig()->get("points");
		$this->banReason = $banInfo["ban reason"];
		foreach($banInfo["ban rules"] as $rule){
			$this->banRules[] = new BanRule($rule);
		}
		$this->actionLogger = new ActionLogger($this);
		$this->getServer()->getPluginManager()->registerEvents($this, $this);
	}
	public function onDisable(){
		$this->provider->dp_close();
	}
	// listener

	// data provider
	public function dp_construct(){
		// TODO init fields
	}
	public function dp_getMain(){
		return $this;
	}
	public function dp_getPoints($ip){
		// TODO Implement dp_getPoints()
		return 0;
	}
	public function dp_close(){
		// TODO unset fields
	}
	// main class - utils
	public function updateBansOfIp($ip){
		$this->getServer()->getIPBans()->removeExpired();
		$entries = $this->getServer()->getIPBans()->getEntries();
		$entry = null;
		if(isset($entries[$ip])){
			$entry = $entries[$ip];
		}
		$points = $this->provider->dp_getPoints($ip);
		$time = time() + $this->getBanPeriod($points);
		$orig = $entry->getExpires();
		if($time > $orig){
			$expiry = new \DateTime;
			$expiry->setTimestamp($time);
			$this->getServer()->getIPBans()->addBan($ip, $this->banReason, $expiry);
		}
	}
	public function getBanPeriod($points){
		$period = 0;
		foreach($this->banRules as $rule){
			$period += $rule->getHours($points);
		}
		return $period;
	}
	public static function wrapAndTruncate($string, $linesCnt, $page, $lineSeparator = "\n"){
		if(is_array($string)){
			$lines = $string;
		}
		else{
			$lines = explode($lineSeparator, $string);
		}
		$max = ceil($lines / $linesCnt);
		$page = max(1, $page);
		$page = min($page, $max);
		$actPage = $page - 1;
		$out = [];
		$delta = 0;
		for($i = 0; $i < $linesCnt; $i++){
			$k = $actPage * $linesCnt + $i + $delta;
			if(isset($lines[$k])){
				$out[] = $lines[$k];
			}
			else{
				break;
			}
		}
		return ["page" => $page, "max" => $max, "output" => implode($lineSeparator, $out)]; // remove the final newline
	}
	public static function convertEOL($string){
		// convert from \r\n to \n
		$string = str_replace("\r\n", "\n", $string);
		// now all the occurrences of \r are mac-style line separators
		// convert from \r to \n
		$string = str_replace("\r", "\n", $string);
		return $string;
	}
	/**
	 * @return ActionLogger
	 */
	public function getActionLogger(){
		return $this->actionLogger;
	}
	/**
	 * @return DataProvider
	 */
	public function getDataProvider(){
		return $this->provider;
	}
}
