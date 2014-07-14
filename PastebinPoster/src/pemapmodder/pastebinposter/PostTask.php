<?php

namespace pemapmodder\pastebinposter;

use pocketmine\scheduler\AsyncTask;
use pocketmine\Server;

class PostTask extends AsyncTask{
	/** @var string */
	private $url;
	/** @var string */
	private $post;
	/** @var int */
	private $timeout;
	private $logger;
	public function __construct($url, $post, $timeout = 3000, \Logger $logger){
		$this->url = $url;
		$this->post = $post;
		$this->timeout = $timeout;
		$logger->debug("Created post task to \"$url\", posting \"$post\" with timeout of $timeout seconds.");
		$this->logger = $logger;
	}
	public function onRun(){
		$res = curl_init($this->url);
		curl_setopt($res, CURLOPT_POST, true);
		curl_setopt($res, CURLOPT_POSTFIELDS, $this->post);
		curl_setopt($res, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($res, CURLOPT_VERBOSE, 1);
		curl_setopt($res, CURLOPT_NOBODY, 0);
		curl_setopt($res, CURLOPT_TIMEOUT_MS, $this->timeout);
		$this->setResult(curl_exec($res));
	}
	public function onCompletion(Server $server){
		/** @var Main $main */
		$main = $server->getPluginManager()->getPlugin("PastebinPoster");
		$main->onCompletion(spl_object_hash($this), $this->getResult());
	}
}
