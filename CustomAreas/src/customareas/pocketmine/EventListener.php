<?php

namespace customareas\pocketmine;

use customareas\area\Area;
use customareas\CustomAreas;
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\block\BlockPlaceEvent;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\level\Position;
use pocketmine\Player;

class EventListener implements Listener{
	/** @var CustomAreas */
	private $main;

	public function __construct(CustomAreas $main){
		$this->main = $main;
	}

	public function onInteract(PlayerInteractEvent $event){
		// TODO
	}

	public function onBlockPlace(BlockPlaceEvent $event){
		if($this->areaHasFlag($event->getPlayer(), Area::FLAG_PLACE, true, true, $event->getBlock())){
			$event->setCancelled();
		}
	}

	public function onBlockBreak(BlockBreakEvent $event){
		if($this->areaHasFlag($event->getPlayer(), Area::FLAG_BREAK, true, true, $event->getBlock())){
			$event->setCancelled();
		}
	}

	/**
	 * @param EntityDamageEvent $event
	 *
	 * @priority        HIGH
	 * @ignoreCancelled true
	 */
	public function onDamage(EntityDamageEvent $event){
		$damaged = $event->getEntity();
		if($damaged instanceof Player){
			$area = $this->main->getDatabase()->searchAreaByPosition($damaged);
			if($area instanceof Area){
				$cause = $event->getCause();
				if($this->areaHasFlag($damaged, Area::FLAG_DAMAGED, false, false)){
					$event->setCancelled();
				}elseif($cause === EntityDamageEvent::CAUSE_BLOCK_EXPLOSION){
					if($this->areaHasFlag($damaged, Area::FLAG_DAMAGED_BY_EXPLOSION, false)){
						$event->setCancelled();
					}
				}elseif($cause === EntityDamageEvent::CAUSE_VOID){
					if($this->areaHasFlag($damaged, Area::FLAG_DAMAGED_BY_VOID, false)){
						$event->setCancelled();
					}
				}elseif($cause === EntityDamageEvent::CAUSE_SUFFOCATION){
					if($this->areaHasFlag($damaged, Area::FLAG_DAMAGED_BY_SUFFOCATE, false)){
						$event->setCancelled();
					}
				}elseif($cause === EntityDamageEvent::CAUSE_DROWNING){
					if($this->areaHasFlag($damaged, Area::FLAG_DAMAGED_BY_DROWN, false)){
						$event->setCancelled();
					}
				}elseif($cause === EntityDamageEvent::CAUSE_FALL){
					if($this->areaHasFlag($damaged, Area::FLAG_DAMAGED_BY_FALL, false)){
						$event->setCancelled();
					}
				}elseif($cause === EntityDamageEvent::CAUSE_FIRE or $cause === EntityDamageEvent::CAUSE_FIRE_TICK or $cause === EntityDamageEvent::CAUSE_LAVA){
					if($this->areaHasFlag($damaged, Area::FLAG_DAMAGED_BY_FIRE, false)){
						$event->setCancelled();
					}
				}elseif($event instanceof EntityDamageByEntityEvent){
					$damager = $event->getDamager();
					if($damager instanceof Player){
						if($this->areaHasFlag($damaged, Area::FLAG_DAMAGED_BY_PLAYER, false)){
							$event->setCancelled();
						}
					}elseif($this->areaHasFlag($damaged, Area::FLAG_DAMAGED_BY_ENTITY, false)){
						$event->setCancelled();
					}
				}
			}
		}
		if(!$event->setCancelled() and ($event instanceof EntityDamageByEntityEvent)){
			$damager = $event->getDamager();
			if($damager instanceof Player){
				if($damaged instanceof Player){
					if(!$this->areaHasFlag($damager, Area::FLAG_DAMAGE_PLAYER, true)){
						$event->setCancelled();
					}
				}elseif(!$this->areaHasFlag($damager, Area::FLAG_DAMAGE_MOB, true)){
					$event->setCancelled();
				}
			}
		}
	}

	/**
	 * @param Player        $player
	 * @param int           $flag
	 * @param bool          $default
	 * @param bool          $partial
	 * @param Position|null $loc
	 *
	 * @return bool
	 */
	private function areaHasFlag(Player $player, $flag, $default, $partial = true, $loc = null){
		if(!($loc instanceof Position)){
			$loc = $player;
		}
		$area = $this->main->getDatabase()->searchAreaByPosition($loc);
		return ($area instanceof Area) ? $area->hasFlag($player->getName(), $flag, $partial) : $default;
	}
}
