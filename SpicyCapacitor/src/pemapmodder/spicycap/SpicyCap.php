<?php

namespace pemapmodder\spicycap;

use pemapmodder\spicycap\database\MySQLDatabase;
use pemapmodder\spicycap\database\SQLite3Database;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\permission\BanEntry;
use pocketmine\plugin\PluginBase;

class SpicyCap extends PluginBase{
	/** @var \pemapmodder\spicycap\database\Database */
	private $database;
	/** @var BanRule[] */
	private $rules = [];
	public function onEnable(){
		$this->saveResource("rules.txt");
		$this->saveDefaultConfig();
		$config = $this->getConfig()->get("database");
		$type = $config["type"];
		switch($type){
			case "SQLite3":
				$opts = $config[$type];
				$this->database = new SQLite3Database($opts["path"], $this);
				break;
			case "MySQLi":
				$opts = $config[$type];
				$mysqli = new \mysqli($opts["host"], $opts["username"], $opts["password"], $opts["database"], $opts["port"]);
				if($mysqli->connect_error){
					$this->getLogger()->critical("Cannot connect to MySQL server. Reason: {$mysqli->connect_error}. SpicyCapacitor will not be enabled.");
					$this->getServer()->getPluginManager()->disablePlugin($this);
					return;
				}
				$this->database = new MySQLDatabase($mysqli, $this);
			default:
				$this->getLogger()->critical("Unknown database type: $type. SpicyCapacitor will not be enabled.");
				$this->getServer()->getPluginManager()->disablePlugin($this);
				return;
		}
		$ptCfg = $this->getConfig()->get("points");
		foreach($ptCfg["ban rules"] as $rule){
			$this->rules[] = new BanRule($rule);
		}
	}
	public function updatePoints($ip){
		$sum = $this->database->getBanPointsSum($ip);
		$secs = 0;
		foreach($this->rules as $rule){
			$secs += $rule->getSeconds($sum);
		}
		if($secs === 0){
			return;
		}
		$list = $this->getServer()->getIPBans();
		$list->remove($ip);
		$entry = new BanEntry($ip);
		$expiry = new \DateTime();
		$expiry->setTimestamp(time() + $secs);
		$entry->setExpires($expiry);
		$entry->setReason($this->getConfig()->get("points")["ban reason"]);
		$list->add($entry);
	}
	public function onCommand(CommandSender $issuer, Command $cmd, $alias, array $args){
		switch($cmd->getName()){
			case "sc-help":
				if(!isset($args[0])){
					$args[0] = "cmds";
				}
				$page = 1;
				if(isset($args[1])){
					$page = intval($args[1]);
				}
				$lines = 5;
				if(isset($args[2])){
					$lines = intval($args[2]);
				}
				switch($args[0]){
					case "flags":
						$help = [
//							"                                                  ", // dummy line: we have 50 spaces here, the standard length of an MCPE chat screen
							"Flags specify the type of report.",
							"If you specify the correct flags,",
							"related logs will be attached with the report",
							"to provide evidence for report reviewers.",
							"When specifying multiple flags",
							"separate them with a comma (',').",
							"",
							"The following is a list of flags.",
							"chat - use this flag when chat logs",
							"  can serve as evidence to the report.",
							"motion - use this flag when logs",
							"  that record speed, directions and coordinates",
							"  of a player can serve as evidence to the report.",
							"",
							"The following are modifiers for flags.",
							"These modifiers can change the range of the logs",
							"so that report reviewers only have",
							"to concentrate on the useful logs.",
							"To use these modifiers, add them after a flag.",
							"-p - add this modifier to the chat flag",
							"  so that only chat messages of the reported",
							"  player are attached with the report.",
							"-f[<time>] - add this modifier to any flags",
							"  so that the logs start from <time>.",
							"  <time> should be replaced by a time period.",
							"  e.g. 2m30s for 2 minutes and 30 seconds.",
							"  Available units are h(hour), m(minute) and",
							"  s(second).",
							"-t[<time>] - add this modifier to any flags",
							"  so that the logs in the past <time> will",
							"  not be added. <time> is the same format as",
							"  in -f[<time>]."
						];
						$issuer->sendMessage(self::breakLines($help, $lines, $page));
						break;
					case "cmds":
						$text = [
							"TODO"
						];
						$issuer->sendMessage(self::breakLines($text, $lines, $page));
						break;
				}
				return true;
		}
		return false;
	}
	public static function breakLines($string, $linesCnt, $page, $lineSeparator = "\n"){
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
//			if($i === 0 or $i + 1 === $linesCnt){
//				while(isset($lines[$k]) and trim($lines[$k]) === ""){
//					$delta++;
//					$k = $actPage * $linesCnt + $i + $delta;
//				}
//				if(!isset($lines[$k])){
//					break;
//				}
//			}
			// too unstable
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
}
