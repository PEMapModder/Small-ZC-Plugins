<?php

namespace pemapmodder\pastebinposter;

use pocketmine\plugin\PluginBase;

class Main extends PluginBase{
	const PRIVACY_PUBLIC = "0";
	const PRIVACY_UNLISTED = "1";
	const PRIVACY_PRIVATE = "2";
	const EXPIRY_NEVER = "N";
	const EXPIRY_TEN_MINUTES = "10M";
	const EXPIRY_HOUR = "1H";
	const EXPIRY_DAY = "1D";
	const EXPIRY_WEEK = "1W";
	const EXPIRY_FORTNIGHT = "2W";
	const EXPIRY_MONTH = "1M";
	private $API_KEY;
	private $MEMBER_CODE = false;
	public function onEnable(){
		$this->saveDefaultConfig();
		$key = $this->getConfig()->get("api key");
		$username = $this->getConfig()->get("username");
		$password = $this->getConfig()->get("password");
		$memberCode = $this->getConfig()->get("member code");
		if(!is_string($key)){
			$this->getLogger()->critical("Invalid API key. Please setup config.yml to use this plugin.");
			$this->setEnabled(false); // I love seeing my plugins suicide :D
		}
		$this->API_KEY = $key;
		$timeout = 3000;
		if(!is_string($memberCode)){
			if(is_string($username) and is_string($password)){
				$this->getLogger()->info("Fetching your member code...");
				$name = urlencode($username);
				$pass = urlencode($password);
				$curl = curl_init("http://pastebinc.com/api/api_login.php");
				curl_setopt($curl, CURLOPT_POST, true);
				curl_setopt($curl, CURLOPT_POSTFIELDS, "api_dev_key=$key&api_user_name=$name" .
					"&api_user_password=$pass");
				curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
				curl_setopt($curl, CURLOPT_VERBOSE, 1);
				curl_setopt($curl, CURLOPT_NOBODY, 0);
				curl_setopt($curl, CURLOPT_TIMEOUT_MS, $timeout);
				$response = curl_exec($curl); // blocks main thread
				if($response === false){
					$this->getLogger()->critical("No response from http://pastebin.com in $timeout " .
						"milliseconds. Check if you are online. Pastes will be posted as a guest.");
				}
				$bad = "bad api request";
				if(strtolower(substr($response, 0, strlen($bad))) === $bad){
					$this->getLogger()->critical("Member code request from http://pastebin.com. " .
						"Reason: ".substr(strstr($response, ", "), 2).". Pastes will be posted as a guest.");
				}
				else{
					$this->MEMBER_CODE = $response;
				}
			}
			else{
				$this->getLogger()->notice("Invalid username/password with no member code " .
					"provided in config.yml. Pastes will be posted as a guest.");
			}
		}
		else{
			$this->MEMBER_CODE = $memberCode;
		}
		$this->getLogger()->info("Successfully initialized.");
	}
	public function onDisable(){
		$this->getLogger()->debug("disabled");
	}
	public function postPaste($content, $name = false, $expiry = self::EXPIRY_WEEK, $format = "text", $privacy = self::PRIVACY_UNLISTED, $timeout = 3000){
		$post = "api_option=paste";
		if(is_string($this->MEMBER_CODE)){
			$post .= "&api_user_key=".$this->MEMBER_CODE;
		}
		$post .= "&api_paste_private=$privacy";
		if(is_string($name)){
			$post .= "&api_paste_name=$name";
		}
		$post .= "&api_paste_expiry_date=$expiry";
		$post .= "&api_paste_format=$format";
		$post .= "&api_dev_key=".$this->API_KEY;
		$post .= "&api_paste_code=$content";
		$this->getServer()->getScheduler()->scheduleAsyncTask($task = new PostTask("http://pastebin.com/api/api_post.php", $post, $timeout, $this->getLogger()));
		return spl_object_hash($task);
	}
	public function onCompletion($taskHash){

	}
}
