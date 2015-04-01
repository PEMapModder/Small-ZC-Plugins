<?php

namespace authtools;

use pocketmine\event\Listener;
use pocketmine\event\player\PlayerCommandPreprocessEvent;

class EventHandler implements Listener{
	/** @var AuthTools */
	private $main;
	public function __construct(AuthTools $main){
		$this->main = $main;
	}
	/**
	 * @param PlayerCommandPreprocessEvent $event
	 * @priority LOWEST
	 */
	public function onPreCmd(PlayerCommandPreprocessEvent $event){
		$ses = $this->main->getSession($p = $event->getPlayer());
		if($ses->chatState === AuthToolsSession::AUTH){
			$event->setMessage($msg = AuthTools::hash(strtolower($p->getName()), $event->getMessage()));
			$event->setCancelled();
			$info = $this->main->sa->getDataProvider()->getPlayer($p);
			if($info === null){
				$this->registerPlayer($event);
			}else{
				$this->loginPlayer($event, $info);
			}
		}elseif(!$p->hasPermission("authtools.chatpw")){
			$hash = AuthTools::hash(strtolower($p->getName()), $event->getMessage());
			$info = $this->main->sa->getDataProvider()->getPlayer($p);
			if(hash_equals($info["hash"], $hash)){
				$event->setCancelled();
				$event->setMessage($hash); // extra security
			}
		}
	}
	private function registerPlayer(PlayerCommandPreprocessEvent $event,
		/** @noinspection PhpUnusedParameterInspection */ $POCKETMINE_IGNORE_ME_PLEASE = null){
		$ses = $this->main->getSession($event->getPlayer());
		switch($ses->chatSubstate){

		}
	}
	private function loginPlayer(PlayerCommandPreprocessEvent $event, $info){
		$player = $event->getPlayer();
		$ses = $this->main->getSession($event->getPlayer());
		/** @var string $hash */
		/** @var string $lastip */
		/** @var int $logindate */
		/** @var int $registerdate */
		extract($info);
		if(hash_equals($hash, $event->getMessage())){
			$this->main->sa->authenticatePlayer($player); // SimpleAuth will tell the login.success message
			$ses->reset();
		}else{
			$ses->chatSubstate++;
			if($ses->chatSubstate >= ($maxFailures = $this->main->getSettings()->maxFailures)){
				$player->kick($this->main->_->_("KeepFailingKickMsg", ["attempts" => $ses->chatSubstate]));
			}else{
				$player->sendMessage($this->main->_->_("LoginFailureMsg", ["attempts" => $ses->chatSubstate, "chances" => $maxFailures - $ses->chatSubstate]));
			}
		}
	}
}
