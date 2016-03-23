<?php

namespace PEMapModder\RemoteChat;

use pocketmine\scheduler\AsyncTask;

class RequestAsyncTask extends AsyncTask{
	/** @var string */
	private $replyTo, $recipient, $message, $ip, $myHostName;
	/** @var int */
	private $queryPort, $listenerPort, $myListenerPort;

	/**
	 * @param string $myHostName
	 * @param int    $myListenerPort
	 * @param string $replyTo
	 * @param string $recipient
	 * @param string $message
	 * @param string $ip
	 * @param int    $queryPort
	 * @param int    $listenerPort
	 */
	public function __construct($myHostName, $myListenerPort, $replyTo, $recipient, $message, $ip, $queryPort = 19132, $listenerPort = 0){
		$this->myHostName = $myHostName;
		$this->myListenerPort = $myListenerPort;
		$this->replyTo = $replyTo;
		$this->recipient = $recipient;
		$this->message = $message;
		$this->ip = $ip;
		$this->queryPort = $queryPort;
		$this->listenerPort = $listenerPort;
	}

	/**
	 * Some code is partially copied from https://github.com/99leonchang/PocketMine-Banners
	 */
	public function onRun(){
		$this->ip = gethostbyname($this->ip);
		if($this->listenerPort === 0){
			$sock = @fsockopen($this->ip, $this->queryPort, $e, $error, 5);
			if($error){
				$this->setResult("Error querying target server: " . $error);
				return;
			}
			socket_set_timeout($sock, 1);
			if(!@fwrite($sock, "\xFE\xFD\x09\x10\x20\x30\x40\xFF\xFF\xFF\x01")){
				$this->setResult("Error querying target server: server down");
				return;
			}
			$challenge = @fread($sock, 1400);
			if(!$challenge){
				$this->setResult("Error querying target server: unknown error");
				return;
			}
			$challenge = substr(preg_replace("/[^0-9\\-]/si", "", $challenge), 1);
			$query = sprintf(
				"\xFE\xFD\x00\x10\x20\x30\x40%c%c%c%c\xFF\xFF\xFF\x01",
				($challenge >> 24),
				($challenge >> 16),
				($challenge >> 8),
				($challenge >> 0)
			);
			if(!@fwrite($sock, $query)){
				$this->setResult("Error querying target server: unknown error");
				return;
			}
			$response = explode("\0", substr(@fread($sock, 2048) . @fread($sock, 2048), 16));
			@fclose($sock);
			array_pop($response);
			array_pop($response);
			array_pop($response);
			array_pop($response);
			for($i = 0; $i < count($response); $i++){
				if(($i & 1) === 0 and $response[$i] === "pm_remotechat"){
					$this->listenerPort = (int) $response[$i + 1];
					break;
				}
			}
			if($this->listenerPort === 0){
				$this->setResult("Error querying target server: RemoteChat not enabled.");
				return;
			}
		}
		$sock = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
		if($sock === false){
			$this->setResult("Error sending request: failed to create socket - " . socket_strerror(socket_last_error()));
			return;
		}
		if(socket_connect($sock, $this->ip, $this->listenerPort) === false){
			$this->setResult("Error sending request: failed to connect to $this->ip:$this->listenerPort - " . socket_strerror(socket_last_error($sock)));
			return;
		}
		socket_write($sock, "REMOTECHAT 1 PRIVMSG $this->myHostName $this->myListenerPort\r\n");
	}

	/**
	 * @param string $result
	 */
	public function setResult($result){
		parent::setResult($result, false);
	}
}
