<?php

namespace votifierpe;

use phpseclib\Crypt\RSA;
use pocketmine\Thread;

class TCPListener extends Thread{
	const CURRENT_PROTOCOL_VERSION = "PE_1.0";
	/** @var VotifierPE */
	private $plugin;
	/** @var bool */
	private $running = true;
	/** @var resource */
	private $socket;
	private $socketClosed = false;
	private $port;
	private $keys;
	public function __construct(VotifierPE $plugin, $port, $keys){
		$this->plugin = $plugin;
		$this->port = $port;
		$this->keys = $keys;
		$this->start();
	}
	public function run(){
		$this->socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
		if(!$this->socket){
			$this->plugin->getLogger()->critical("Cannot create TCP server socket!");
			return;
		}
		if(!socket_bind($this->socket, "0.0.0.0", $this->port)){
			$this->plugin->getLogger()->critical("Cannot bind TCP server socket to 0.0.0.0:$this->port. Is a server running on that port?");
			return;
		}
		if(!socket_listen($this->socket, 5)){
			$this->plugin->getLogger()->critical("Cannot listen to TCP server socket!");
			return;
		}
		while($this->running){
			$con = socket_accept($this->socket);
			socket_write($con, "VOTIFIER " . self::CURRENT_PROTOCOL_VERSION);
			$cipher = socket_read($con, 256);
			$keys = unserialize($this->keys);
			$rsa = new RSA;
			$rsa->loadKey($keys["publickey"]);
			$plain = $rsa->decrypt($cipher);
			list($vote, $service, $username, $address, $timestamp) = explode("\n", $plain);
			if($vote !== "VOTE"){
				socket_close($con);
			}
			$array = [
				"service" => $service,
				"username" => $username,
				"address" => $address,
				"timestamp" => $timestamp,
			];
			$serialized = serialize($array);
			$this->plugin->queue(function(VotifierPE $plugin) use($serialized){
				$plugin->onVoteReceived(unserialize($serialized));
			});
			socket_close($con);
		}
		socket_close($this->socket);
		$this->socketClosed = true;
	}
	public function stop(){
		$this->running = false;
	}
	public function __destruct(){
		if(!$this->socketClosed){
			socket_close($this->socket);
			$this->socketClosed = true;
		}
	}
}
