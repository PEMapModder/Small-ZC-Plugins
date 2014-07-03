<?php

namespace pemapmodder\dst;

use pocketmine\event\Listener;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\plugin\PluginBase;

class Main extends PluginBase implements Listener{
	public static $dstCnt = 0;
	/** @var Store[] */
	private $stores = [];
	public function onEnable(){
		@mkdir($d = $this->getDataFolder());
		if(file_exists($d."dst_cnt.txt")) self::$dstCnt = (int) file_get_contents($d."dst_cnt.txt");
		@mkdir($this->worldsData = $d."worlds/");
		$this->getServer()->getPluginManager()->registerEvents($this, $this);
		$this->getServer()->getScheduler()->scheduleDelayedRepeatingTask(new TickUpdater($this), 10);
	}
	public function add(DynamicSignHandler $ds){
		return $this->stores[$ds->getLevel()->getName()]->add($ds);
	}
}
