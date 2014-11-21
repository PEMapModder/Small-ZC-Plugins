<?php

namespace votifierpe;

use phpseclib\Crypt\RSA;
use pocketmine\plugin\PluginBase;

class VotifierPE extends PluginBase{
	/** @var bool */
	private $lock = false;
	/** @var callable[] */
	private $queue = [];
	/** @var TCPListener */
	private $tcp;
	/** @var VoteListener[] */
	private $listeners = [];
	public function onLoad(){
		try{
			class_exists("phpseclib\\Crypt\\RSA", true);
		}
		catch(\RuntimeException $e){}
	}
	public function onEnable(){
		$this->saveDefaultConfig();
		$keyFile = $this->getDataFolder() . "key.json";
		if(!is_file($keyFile)){
			$dir = dirname($keyFile);
			if(!is_dir($dir)){
				mkdir($dir, 0777, true);
			}
			$rsa = new RSA;
			$keys = $rsa->createKey(2048);
			file_put_contents($keyFile, json_encode($keys, JSON_PRETTY_PRINT | JSON_BIGINT_AS_STRING));
		}
		else{
			$keys = json_decode(file_get_contents($keyFile), true);
		}
		$listenerDir = $this->getDataFolder() . "listeners/";
		if(!is_dir($listenerDir)){
			mkdir($listenerDir);
		}
		foreach(scandir($listenerDir) as $file){
			if(is_file($listenerDir . $file) and substr($file, -4) === ".php"){
				require_once $listenerDir . $file;
				$contents = file_get_contents($listenerDir . $file);
				if(preg_match_all('/^class=([A-Za-z0-9_]+)$/m', $contents, $matches, PREG_PATTERN_ORDER)){
					$class = $matches[1][0];
					try{
						$reflection = new \ReflectionClass($class);
						if(!$reflection->isInstantiable()){
							$this->getLogger()->error("Unable to instantiate class $class specified at vote listener $file!");
							continue;
						}
						if(!$reflection->isSubclassOf("votifierpe\\VoteListener")){
							$this->getLogger()->error("Class $class of vote listener $file must implement votifierpe\\VoteListener but didn't!");
							continue;
						}
						$this->listeners[] = $reflection->newInstance($this);
					}
					catch(\ReflectionException $e){
						$this->getLogger()->error("Unable to load vote listener $file: unable to find specified listener class $class");
						continue;
					}
				}
			}
		}
		$this->tcp = new TCPListener($this, $this->getConfig()->get("port"), serialize($keys));
	}
	public function queue(callable $runnable){
		$this->acquire();
		$this->queue[] = $runnable;
		$this->release();
	}
	public function acquire(){
		while($this->lock);
		$this->lock = true;
	}
	public function release(){
		$this->lock = false;
	}
	public function onVoteReceived(array $vote){
		foreach($this->listeners as $listener){
			if($listener->listen($vote) === false){
				return;
			}
		}
		$this->getServer()->getPluginManager()->callEvent(new VotifierPEVoteEvent($vote));
	}
}
