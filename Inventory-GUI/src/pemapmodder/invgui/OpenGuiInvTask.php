<?php

namespace pemapmodder\invgui;

use pocketmine\Player;
use pocketmine\item\Item;
use pocketmine\scheduler\PluginTask;
use pocketmine\plugin\Plugin;

class OpenGuiInvTask extends PluginTask{
	public function __construct(Plugin $plugin, Player $player){
		parent::__construct($plugin);
		$this->player = $player;
	}
	public function onRun($ticks){
		$pk = new ContainerOpenPacket;
		$id = $player->windowCnt = $pk->windowId = max(2, $player->windowCnt % 99);
		$pk->type = 0;
		$pk->slots = 36;
		$pk->x = 0;
		$pk->y = 0;
		$pk->z = 0;
		$this->player->dataPacket($pk);
		$slots = array();
		for($i = 0; $i < 27; $i++)
			$slots[] = Item::get(0);
	}
}
