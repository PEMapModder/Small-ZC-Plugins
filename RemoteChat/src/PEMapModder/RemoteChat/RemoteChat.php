<?php

namespace PEMapModder\RemoteChat;

use pocketmine\event\Listener;
use pocketmine\event\server\QueryRegenerateEvent;
use pocketmine\plugin\PluginBase;

class RemoteChat extends PluginBase implements Listener{
	const NOREPLY = "RemoteChat Plugin";
	/** @var ListenerThread */
	private $thread = null;

	public function onEnable(){
		$this->saveDefaultConfig();
		$this->saveResource("Documentation.md", true); // update it every time
		$this->thread = new ListenerThread($this->getConfig()->getNested("listener.port", 44746), $this->getConfig()->get("blacklist", []), $this->getConfig()->getNested("whitelist.ips", []), $this->getConfig()->getNested("whitelist.enabled", false));
		$this->thread->start();
		$this->getServer()->getCommandMap()->register("remchat", new RemoteChatCommand($this));
		$this->getServer()->getPluginManager()->registerEvents($this, $this);
		$this->getServer()->getScheduler()->scheduleDelayedRepeatingTask(new PullSyncTask($this), 40, 5);
	}

	public function onQueryRegen(QueryRegenerateEvent $event){
		if($this->thread === null){
			return;
		}
		if($this->thread->terminated){
			return;
		}
		$extraData = $event->getExtraData();
		$extraData["pm_remotechat"] = $this->getConfig()->getNested("listener.port", 44746);
		$event->setExtraData($extraData);
	}

	public function tick(){
		$data = $this->thread->pullData();
		/** @var int $c */
		extract($data);
		if($c === ListenerThread::CLASS_LOG){
			/** @var string $lm */
			/** @var string $ll */
			$this->getLogger()->log($ll, $lm);
		}elseif($c === ListenerThread::CLASS_REQUEST){
			/** @var string $ra */
			/** @var array $rp */
			/** @var string $_ip */
			/** @var int $_port */
			switch($ra){
				case "PRIVMSG":
					$this->PRIVMSG($_ip, $_port, $rp);
			}
		}
	}

	private function PRIVMSG($ip, $port, $params){
		list($replyTo, $recipient, $message) = $params;
		// TODO send to $recipient
		if($replyTo !== self::NOREPLY){
			$this->sendPRIVMSG2(self::NOREPLY, $replyTo, "Message received", $ip, 0, $port);
		}
	}

	/**
	 * @param string $replyTo
	 * @param string $recipient
	 * @param string $message
	 * @param string $ip
	 * @param int    $queryPort - if $listenerPort is given, this value will be ignored.
	 * @param int    $listenerPort
	 */
	public function sendPRIVMSG2($replyTo, $recipient, $message, $ip, $queryPort = 19132, $listenerPort = 0){
		$this->getServer()->getScheduler()->scheduleAsyncTask(new RequestAsyncTask($this->getConfig()->getNested("listener.display-ip", ""), $this->getConfig()->getNested("listener.port", 44746), $replyTo, $recipient, $message, $ip, $queryPort, $listenerPort));
	}
}
