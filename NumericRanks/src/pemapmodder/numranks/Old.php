<?php

class NumRkPlugin implements Plugin{
	////////////////////////////////////////////////////
	// PocketMine interface and initialization stuffs //
	////////////////////////////////////////////////////
	public function init(){
		$this->server=ServerAPI::request();
		$this->dir=$this->server->api->plugin->configPath($this);
		@mkdir($this->dir."players/");
		$this->initialize();
	}
	private function initialize(){
		$this->initCfgs();
		$this->initCmdAndAlias();
		$this->addHandlers();
		$this->autoConfig();
	}
	private function initCfgs(){
		$this->initRankCfg();
		$this->initCmdsCfg();
		$this->initGenCfg();
	}
	private function initRankCfg(){
		$path=$this->dir."rank-names.";
		$ext="yml";
		if(is_file($path."txt"))
			$ext="txt";
		$this->ranks=new Config($path.$ext, CONFIG_YAML, array(
			"default"=>"player",
			"watched"=>-50,
			"player"=>0,
			"vip"=>50,
			"trusted"=>100,
			"admin"=>150,
			"owner"=>200
			// "console"=>PHP_INT_MAX,
			// "rcon"=>PHP_INT_MAX-1
		));
		if(false===$this->ranks->get($this->ranks->get("default")))
			console("[ERROR] Default rank permission not found. Strange bugs may occur.");
	}
	private function initCmdsCfg(){
		$path=$this->dir."rank-permissions.";
		$ext="yml";
		if(is_file($path."txt"))
			$ext="txt";
		$this->perms=new Config($path.$ext, CONFIG_YAML, array(
			"ban"=>150,
			"banip"=>150,
			"defaultgamemode"=>200,
			"deop"=>200,
			"difficulty"=>150,
			"gamemode"=>100,
			"give"=>100,
			"help"=>-100,
			"kick"=>100,
			"kill"=>100,
			"list"=>-100,
			"me"=>50,
			"numrank"=>200,
			"op"=>200,
			"ping"=>-100,
			"save-all"=>200,
			"save-off"=>200,
			"save-on"=>200,
			"say"=>50,
			"seed"=>50,
			"spawn"=>50,
			"spawnpoint"=>100,
			"status"=>0,
			"stop"=>PHP_INT_MAX,
			"sudo"=>150, // consider 200
			"tell"=>50,
			"time"=>100,
			"tp"=>100,
			"whitelist"=>PHP_INT_MAX,
		));
	}
	private function initGenCfg(){
		$path=$this->dir."general-config.";
		$ext="yml";
		if(is_file($path."txt"))
			$ext="txt";
		$this->config=new Config($path.$ext, CONFIG_YAML, array(
			"chat-override after name tag"=>"{@world} <@rank>=> @message",
			"cmd-echo"=>true,
			"cmd-echo-list"=>array("console", "README: You can add player names here"), // honestly I nearly added my own name into it by accident
			// "chat-only-broadcast-in-that-world"=>false, // follow the tradition of PocketMine
		));
	}
	private function initCmdAndAlias(){
		$c=$this->server->api->console;
		$c->register("numrank", "<cmd|help> [args ...] NumericRanks command", array($this, "cmd"));
		$c->alias("nrcmd", "numrank cmd");
		$c->alias("nrplyr", "numrank player");
	}
	private function addHandlers(){
		$this->server->addHandler("player.chat", array($this, "onChat"));
		$this->server->addHandler("console.check", array($this, "cslCheck"), 0x1000); // fully monopolize the permissions
		$this->server->addHandler("console.command", array($this, "cslCmd"), 0x1000); // fully monopolize the permissions
		// fourth word stands for the return type, third is set/get, second is subcmd, first is plugin name. Fifth onwards are miscellaneous info
		$this->server->addHandler("numrk.player.get.int", array($this, "getPlyrPerm"));
		$this->server->addHandler("numrk.rank.get.string", array($this, "getRankName"));
		$this->server->addHandler("numrk.rank.getdefault.string", array($this, "getDefaultRankName"));
		$this->server->addHandler("numrk.rank.get.int", array($this, "getRankIndex"));
		$this->server->addHandler("numrk.cmd.get.int.min", array($this, "getCmdMinPerm"));
		$this->server->addHandler("numrk.instance.get", array($this, "getInstance"));
		// alias
		$this->server->addHandler("numrank.player.get.int", array($this, "getPlyrPerm"));
		$this->server->addHandler("numrank.rank.get.string", array($this, "getRankName"));
		$this->server->addHandler("numrank.rank.getdefault.string", array($this, "getDefaultRankName"));
		$this->server->addHandler("numrank.rank.get.int", array($this, "getRankIndex"));
		$this->server->addHandler("numrank.cmd.get.int.min", array($this, "getCmdMinPerm"));
		$this->server->addHandler("numrank.instance.get", array($this, "getInstance"));
	}
	private function autoConfig(){
		foreach($this->server->api->plugin->getList() as $p){
			switch(strtolower($p["name"])){
				// listed according to the most downloads list on pocketmine forums
				case "worldeditor":
					$this->setCmdPerm("/", 150, true);
					$this->setCmdPerm(array("/copy", "/paste", "/cut", "/replace", "/cut", "/pos1", "/pos2"), 150, true);
					break;
				case "simpleauth":
					$this->setCmdPerm(array("login", "register"), -50, true);
					$this->setCmdPerm("unregister", 0, true);
					break;
				case "economys": // @onebone, thanks for making such a convenient list!
					$this->setCmdPerm(array("mymoney", "mydebt", "topmoney", "takedebt", "returndebt", "economys", "bank"), 0, true);
					$this->setCmdPerm(array("setmoney", "givemoney", "takemoney", "seemoney"), 150, true); // /moneyload and /moneysave? Sorry can't help
					break;
				case "economyjob":
					$this->setCmdPerm("job", 0, true);
					break;
				case "economyland":
					$this->setCmdPerm(array("startp", "endp", "land", "landsell"), 50, true);
					break;
				case "economypayment":
					$this->setCmdPerm("pay", 0, true);
					break;
				case "economypshop":
					$this->setCmdPerm("itemcloud", 50, true);
					break;
				case "economyauction":
					$this->setCmdPerm("auction", 50, true);
					break;
				case "economycasino":
					$this->setCmdPerm("casino", 50, true);
					break;
				case "economyproperty":
					$this->setCmdPerm("property", 50, true);
					break;
				case "portal": /*支持同胞，但不支持簡體字的同胞！*/
					$this->setCmdPerm("por", 100, true);
					break;
				// outdated plugins omitted
				// plugins I don't use and without online documentation omittedd
				// PermissionPlus shouldn't be used along with this plugin
				case "pocketguard":
					$this->setCmdPerm("pg", 50, true);
					break;
				case "simpleworlds":
					$this->setCmdPerm("simpleworlds", 200, true);
					break;
				// enough. Those coming next are either useless plugins or outdated plugins. Then the next ones have too few downloads.
			}
		}
	}
	public function __construct(ServerAPI $a, $s=0){
	}
	public function __destruct(){
	}
	////////////////
	// Public API //
	////////////////
	/**
	 * Sets the permission index of the command
	 * @param mixed $cmd String or array of string of target command to set/add
	 * @param int $perm Integer or array of integer of permission index
	 * @return boolean <code>true</code> on success, <code>false</code> on reject (because of IllegalArguments)
	 */
	public function setCmdPerm($cmd, $perm, $ifNotSet=false){
		if(is_array($cmd)){
			if(is_int($perm)){
				$new=array();
				for($i=0; $i<count($cmd); $i++)
					$new[]=$perm;
				$perm=$new;
			}
			elseif(!is_array($perm))
				return false;
			if(count($cmd)!=count($perm))
				return false;
			foreach($cmd as $c){
				$this->setCmdPerm($c, $perm, $ifNotSet);
			}
		}
		if($ifNotSet and isset($this->perms->getAll()[$cmd]))
			return true;
		if(!is_int($perm))
			return false;
		$this->perms->set($cmd, $perm);
		return true;
	}
	/**
	 * Gets the permission index of the rank with name given
	 * @param String $rankName Name of the rank
	 * @return int Index of the rank
	 */
	public function getRankIndex($name){
		if($name==="default")
			$name=$this->ranks->get("default");
		return $this->ranks->get($name);
	}
	/**
	 * Gets the default rank name
	 * @return int The name of the default rank
	 */
	public function getDefaultRankName(){
		return $this->ranks->get("default");
	}
	/**
	 * Gets the player's permission index
	 * @param mixed $player Any variables that can be directly used as strings ("$plyr")
	 * @return int the permission index of the rank of the player
	 */
	public function getPlyrPerm($player){
		$file=@file_get_contents($this->dir."players/".strtolower(trim("$player")));
		if($file===false){
			$file=$this->ranks->get($this->ranks->get("default"));
		}
		return (int)$file;
	}
	/**
	 * Gets the user-defined name of the rank at $index
	 * @param int $index index of the rank
	 * @return String the name of the rank
	 */
	public function getRankName($index){
		if(!is_numeric($index) or ($ret=array_search($index, $this->ranks->getAll()))){
			$ret="$index";
			foreach($this->ranks->getAll() as $rk=>$idx){
				if($idx<=$index)
					$ret=$rk;
			}
			return $ret;
		}
		return $ret;
	}
	/**
	 * A shortcut for getPlyrPerm() and getRankName().
	 * @param mixed $player Any variables that can be directly used as strings ("$player")
	 * @return String the name of the rank. Same as <code>MainServer::dhandle("numrk.rank.get.string", MainServer::dhandle("numrk.player.get.int"));</code>
	 */
	public function getPlyrPermName($plyr){
		return $this->getRankName($this->getPlyrPerm($plyr)); // server handler function
	}
	/**
	 * Gets an instance of a NumRkPlugin directly.
	 * @return NumRkPlugin $this
	 */
	public function getInstance(){
		return $this;
	}
	/**
	 * Sets a player's permission
	 * @param mixed $player Any variables that can be directly used as strings ("$player")
	 * @param int $perm The index of the target rank
	 */
	public function setPlyrPerm($player, $perm){
		file_put_contents($this->dir."players/".strtolower(trim("$player")), (int)$perm);
	}
	/**
	 * Gets the minimum rank's permission index to run the target command.
	 * @param String $cmd The name of the target command.
	 * @return int The index as defined in the config file, or <code>false</code> if not defined.
	 */
	public function getCmdMinPerm($cmd, $alias=false){
		$index=$this->perms->get($cmd);
		$idx2=false;
		if(is_string($alias))
			$idx2=$this->perms->get($alias);
		if(!is_numeric($index) and !is_numeric($index))
			return false;
		if(is_float($index))
			console("[WARNING] NumericRanks reads the permission index of /$cmd as $index, which is not an integer. Please note that strange errors may occur if the indices are not integer.");
		if(is_float($idx2))
			console("[WARNING] NumericRanks reads the permission index of /$alias as $idx2, which is not an integer. Please note that strange errors may occur if the indices are not integer.");
		if(!is_numeric($idx2))
			return $index;
		if(!is_numeric($index)){
			$index=$idx2;
			return $index;
		}
		// now we have two values, $index being the command permission, and $idx2 being the alias permission. Which one should we use? The larger one? The smaller one? Or $idx2? Tell me your comment on https://github.com/PEMapModder/NumericRanks/issues/2
		return $idx2;
	}
	/**
	 * Gets the config-defined message to be sent in chat.
	 * @param Player $p The speaker of the message
	 * @param string $message The message spoken.
	 * @return string The processed message to be output (I think past tense for "output" is also "output")
	 */
	public function getChat($p, $msg){
		$idx=$this->getPlyrPerm($p);
		// console("[DEBUG] $p index $idx");
		$name=$this->getRankName($idx);
		return $this->parseCfgStrStable($this->config->get("chat-override"), array("rank", "message", "msg", "world"), array($name, $msg, $msg, $p->entity->level->getName()));
	}
	////////////////////
	// Event handlers //
	////////////////////
	public function onChat(&$data){
		$data["message"]=$this->getChat($data["player"], $data["message"]);
	}
	// handler for event "console.check"
	public function cslCheck($data){
		$pp=$this->getPlyrPerm($data["issuer"]);
		$cmp=$this->getCmdMinPerm($data["cmd"], $data["alias"]);
		if($cmp===false)
			$cmp=$this->ranks->get($this->ranks->get("default"));// Warning has already been generated at "console.command" event.
		if($pp>=$cmp)
			return true;
	}
	// handler for event "console.command"
	public function cslCmd($data){
		if(is_string($data["issuer"]))
			return;
		if($data["cmd"]=="login" or $data["cmd"]=="register") // Security! How can I increase this security level while I want to make it remain in PHP (not PMF) and also open source?
			$data["parameters"]=array("[NumRank]", "[CmdEcho]", "Hidden", "for", "safety", "reasons");
		if($this->config->get("cmd-echo"))
			$this->cmdEcho($data["issuer"], "/".$data["cmd"]." ".implode(" ", $data["parameters"]), @$data["alias"]);
		$pp=$this->getPlyrPerm($data["issuer"]);
		$cmp=$this->getCmdMinPerm($data["cmd"], $data["alias"]);
		if($cmp===false){
			console("[WARNING] The permission index for command".($data["alias"]===false?" /".$data["cmd"]." is ":"s (/".$data["cmd"]." and /".$data["alias"].") are ")."undefined. Default (".$this->getDefaultRankName().") will be used.");
			$cmp=$this->getRankIndex("default");
		}
		if($pp<$cmp)
			return false;
	}
	// fix for /help spam
	public function setMute($args){
		$this->mutes[$args[0]]=$args[1];
	}
	// outputer and processor for CommandEcho
	public function cmdEcho($issuer, $line, $alias=false){
		$path="\$this->mutes[\$issuer->iusername]";
		eval("\$mute=isset($path) and $path===true;");
		if($mute)return;
		if(substr($line, 6)==="/help "){
			$this->server->schedule(1, array($this, "setMute"), array($issuer->iusername, false));
			$this->setMute($issuer->iusername, true);
		}
		if($alias!==false)
			$line.=" with alias /$alias";
		foreach($this->config->get("cmd-echo-list") as $echoee){
			if($echoee==="console")
				console(FORMAT_LIGHT_PURPLE."[NumRank CmdEcho] $issuer has run command $line");
			else{
				$p=$this->server->api->player->get($echoee, false); // no alike
				if($p instanceof Player)
					$p->sendChat("$msg has been run by $issuer."); // start with / to be grey!
			}
		}
	}
	// stable and simple version to parse the chat string (only replaces "@xxx" with given data)
	public function parseCfgStrStable($str, $params=array(), $data=array()){
		foreach($params as $key=>$name){
			$str=str_replace("@".$name, $data[$key], $str);
		}
		return $str;
	}
	/*
	 * An experimental and unused ConditionalStringsParser just purely added here for nothing
	 * @code <code>
	public function parseConStr2Unused($conStr, $names=array(), $data=array()){ // @"PEMapModder's take on RegExp"=="true"=>"just a mess":"quickly finish this plugin"
		if(preg_match_all("#(^@)@([@a-zA-Z0-9_!\"'=<>: ]{1,})#", $conStr, $matches)>0){
			$newStr=strstr($conStr, "@");
			foreach($matches[0] as $escape){
				$savedLits=array(); // array_shift()
				if(preg_match_all("#\"(.*)(^\\)\"#", $escape, $literals)>0){
					$newEsc=$escape;
					$noLit=strstr($newEsc, "\"", true);
					foreach($literals[1] as $literal){
						$savedLits[]=$this->parseConStr2Unused(str_replace("\\\"", "\"", $literal), $names, $data);
						$noLit.="\"";
						$noLit.=strstr($newEsc, "\"", true);
					}
				}
				if(preg_match_all("#@([@a-zA-Z0-9_])*(>|<|((!|=|<|>)=))(@([a-zA-Z0-9_])*|\")=>\":\"", $escape, $conditions)){ // Am I crazy...
					foreach($conditions[0] as $condition){
						$con=preg_split("#(>|<|((!|=|<|>)=))#", explode("=>", $condition, 2)[0]);
						$trueBlock=explode("=>", $condition)[1];
						$falseBlock=explode(":", $condition)[1];
						$conLeft=$con[0];
						$conRight=$con[1];
						// literals
						$tmpConl=$conLeft;
						$conLeft="";
						for($i=0; $i<strlen($tmpConl); $i++){
							if($tmpConl{$i}=="\"" and count($savedLits)>0)
								$conLeft.=array_shift($savedLits);
							else $conLeft.=$tmpConl{$i};
						}
						$tmpConl=$conRight;
						$conRight="";
						for($i=0; $i<strlen($tmpConl); $i++){
							if($tmpConl{$i}=="\"" and count($savedLits)>0)
								$conRight.=array_shift($savedLits);
							else $conRight.=$tmpConl{$i};
						}
						$tmpConl=$trueBlock;
						$trueBlock="";
						for($i=0; $i<strlen($tmpConl); $i++){
							if($tmpConl{$i}=="\"" and count($savedLits)>0)
								$trueBlock.=array_shift($savedLits);
							else $trueBlock.=$tmpConl{$i};
						}
						$tmpConl=$falseBlock;
						$falseBlock="";
						for($i=0; $i<strlen($tmpConl); $i++){
							if($tmpConl{$i}=="\"" and count($savedLits)>0)
								$falseBlock.=array_shift($savedLits);
							else $falseBlock.=$tmpConl{$i};
						}
						#conditions
						if($this->parseConStr2Unused($conLeft, $names, $data) == $this->parseConStr2Unused($conRight, $names, $data))
							$result=$this->parseConStr2Unused($trueBlock);
						else $result=$this->parseConStr2Unused($falseBlock);
					}
					$result="";#TODO
				}
				preg_match_all("#@([a-zA-Z0-9_]{1,})#", $escape, $nameAll);
				foreach($nameAll[0] as $name){
					if(($k=array_search(substr($name, 1), $names)))
						$newStr.=$data[$k];
				}
			}
		}
	}</code>
	*/
	// command redirector for NumericRank (redirects commands into function callings)
	public function cmd($cmd, $args, $issuer){
		$cmd=array_shift($args);
		switch($cmd){
			case "cmd":
				if(count($args)<2 or !is_numeric($args[1]))
					return "Usage: /numrank cmd <cmd> <minimum permission index>";
				$this->perms->set($args[0], (int) $args[1]);
				$this->perms->save();
				return "Minimal permission index if /".$args[0]." set to ".$args[1];
			case "player":
				$usage="Usage: /numrank player <player> <permission>\n";
				if(!isset($args[0])){
					$arrayList=$this->getAllNonDefaultRanks();
					$max=12;
					foreach(array_keys($arrayList) as $player){
						$max=max($max, strlen($player)+1);
					}
					$output="Player name";
					$output=$this->insertAfterStr(" ", $max-strlen($output), $output);
					$output.="| Rank\n";
					foreach($arrayList as $player=>$rank){
						$output.=$player;
						$output=$this->insertAfterStr(" ", $max-strlen($player), $output);
						$output.="| $rank\n";
					}
					return $usage.$output;
				}
				if(!($p=$this->server->api->player->get($args[0])) instanceof Player)
					return "Player ".$args[0]." not found. $usage";
				if(!isset($args[1]))
					return "$p has permission ".(($perm=$this->getRankName($idx=$this->getPlyrPerm($p)))===false?"#$idx":"rank $perm");
				if(is_numeric($args[1])){
					$this->setPlyrPerm($p, (int)$args[1]);
					return "$p's permission index is set to ".$args[1];
				}
				if(($rk=$this->ranks->get($args[1]))!==false){
					$this->setPlyrPerm($p, $rk);
					$p->sendChat("Your permission has been set to ".$args[1]."! (index $rk)");
					return "$p's permission is set to ".$args[1]." (index $rk)";
				}
				else return "Rank ".$args[1]." not found. Use /numrank rank ".$args[1]." <permission index> to set a new permission";
			case "rank":
				if(!isset($args[0])){
					$all=$this->ranks->getAll();
					$max=4;
					foreach($all as $rank=>$i){
						$max=max(strlen($rank), $max);
					}
					$max++;
					$output="List of ranks\n";
					$output.="Rank";
					$output=$this->insertAfterStr(" ", $max-4, $output);
					$output.="| Permission index\n";
					foreach($all as $rank=>$index){
						$output.=$rank;
						$output=$this->insertAfterStr(" ", $max-strlen($rank), $output);
						$output.="| $index\n";
					}
					return $output;
				}
				if(is_numeric($args[0]))
					return "Rank names must not be numeric";
				if(!isset($args[1]))
					return "The permission index for rank ".$args[0]." is ".(($idx=$this->ranks->get($args[0]))===false?"undefined":$idx);
				if(!is_numeric($args[1]))
					return "Please give a numeric permission index.";
				$exist=$this->ranks->get($args[0]);
				$this->ranks->set($args[0], $args[1]);
				$this->ranks->save();
				if($exist!==false)
					return "Set the permission index of rank ".$args[0]." to ".$args[1];
				return "Added rank ".$args[0]." with permission index of ".$args[1];
			case "rmrank":
				$all=$this->ranks->getAll();
				if(!isset($all[$args[0]]))
					return "Rank ".$args[0]." does not exist!";
				unset($all[$args[0]]);
				$this->ranks->setAll($all);
				$this->ranks->save();
				if($this->ranks->get($args[0])===false)//debug
					return "Remove rank ".$args[0]." success!";
				return "Error while trying to remove rank ".$args[0]."!";
			case "help":
				$this->tell($issuer, "Showing help of command /numrank...");
			default:
				return "--Note: perm stands for permission--\n/numrank cmd <cmd> <min perm index> Set a command's minimal permission index\n/numrank player <player> <perm|perm index> Set a player's permission rank or permission index\n/numrank rank <rank> <index> Add/set a rank's permission index\n/numrank rmrank <rank> Remove a rank";
		}
	}
	// a fast method for issuing sendChat() to non-Player-object issuers
	public function tell($rcp, $msg){
		if(is_string($rcp))
			return console("[NumRank] $msg");
		if($rcp instanceof Player)
			return $rcp->sendChat("$msg");
		else return false;
	}
	// Inserts $repeater after $string for $times times and return the string. Widely used in UI-commands.
	public function insertAfterStr($repeater, $times, $string){//I don't have the courage to pass by reference
		while($times>0){
			$string.=$repeater;
			$times--;
		}
		return $string;
	}
	// returns a list of player names as keys and player ranks as values for all non-default ranked players.
	public function getAllNonDefaultRanks(){
		$ret=array();
		$dir=$this->dir."players/";
		$directory=dir($this->dir."players/");
		while(($file=$directory->read())!==false){
			if(is_file($dir.$file)){
				$ret[$file]=(int)file_get_contents($dir.$file);
				if($ret[$file]===$this->ranks->get($this->ranks->get("default"))){
					unset($ret[$file]);
					console("[NOTICE] A data file of $file found as default rank. Deleting this file to optimize performance and save your disk's space... ", false);
					console(@unlink($dir.$file)===true?"Success.":"Failed.");
				}
			}
		}
		return $ret;
	}
	// reserved blank line
}
