<?php

namespace pemapmodder\ircbridge\bridge;

use pemapmodder\ircbridge\bridge\protocol\IRCLine;
use pocketmine\Thread;

class IRCServer extends Thread{
	/** @var Buffer */
	private $buffer;
	/** @var bool */
	private $running = true;
	/** @var resource */
	private $serverSocket;
	/** @var resource[] */
	private $sockets = [];

	/** @var string */
	public $init_error;
	public function __construct(Buffer $buffer, $ip, $port){
		$this->buffer = $buffer;
		$this->serverSocket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
		if($this->serverSocket === false){
			$this->init_error = "Could not create socket!";
			return;
		}
		if(socket_bind($this->serverSocket, $ip, $port) === false){
			$this->init_error = "Could not bind to $ip:$port - is another server running on that TCP/IP port?";
			return;
		}
		if(socket_listen($this->serverSocket, 128) === false){
			$this->init_error = "Could not listen to socket!";
			return;
		}
		socket_set_nonblock($this->serverSocket);
		$this->start();
	}
	public function run(){
		while($this->running){
			while($this->buffer->hasMoreWrite()){
				$line = IRCLine::parseInternalLine($this->buffer->nextWrite(), $signal, $client);
				if($signal === IRCLine::SIGNAL_STD_LINE){
					if(isset($this->sockets[$client])){
						socket_write($this->sockets[$client], $line . "\r\n");
					}
				}elseif($signal === IRCLine::SIGNAL_CLOSE_SESSION){
					if(isset($this->sockets[$line])){
						socket_close($this->sockets[$line]);
						unset($this->sockets[$line]);
					}
				}
			}
			while(($newSock = socket_accept($this->serverSocket)) !== false){
				socket_getpeername($newSock, $address, $port);
				$identifier = "$address:$port";
				$this->buffer->addRead(IRCLine::SIGNAL_OPEN_SESSION . $identifier);
				$this->sockets[$identifier] = $newSock;
			}
			while(($line = $this->readLine($identifier)) !== false){
				$this->buffer->addRead(IRCLine::SIGNAL_STD_LINE . "$identifier $line");
			}
		}
		foreach($this->sockets as $socket){
			socket_write($socket, "ERROR :Server shutting down\r\n");
			socket_close($socket);
		}
		socket_close($this->serverSocket);
	}
	private function readLine(&$identifier){
		foreach($this->sockets as $identifier => $sk){
			$line = socket_read($sk, 512, PHP_NORMAL_READ);
			if($line){
				return $line;
			}elseif(($err = socket_last_error($sk)) >= SOCKET_ENOTSOCK){
				socket_write($sk, "ERROR :Read error: " . socket_strerror($err) . "\r\n");
				socket_close($sk);
				$this->buffer->addRead(IRCLine::SIGNAL_CLOSE_SESSION . $identifier);
			}
		}
		return false;
	}
	public function stop(){
		$this->running = false;
		$this->join();
	}
}
