<?php

namespace rarfix;

use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\Listener;
use pocketmine\plugin\PluginBase;

class RARFix extends PluginBase implements Listener{
	public function onEnable(){
		$this->getServer()->getPluginManager()->registerEvents($this, $this);
	}
	/**
	 * @param EntityDamageEvent $event
	 * @priority LOWEST
	 */
	public function onDamage(EntityDamageEvent $event){
		if($event->isApplicable(EntityDamageEvent::MODIFIER_ARMOR) and
			($damage = $event->getDamage(EntityDamageEvent::MODIFIER_ARMOR)) > 0){
			$event->setDamage(-floor($event->getDamage() * 0.04 * $damage), EntityDamageEvent::MODIFIER_ARMOR);
		}
	}
}
