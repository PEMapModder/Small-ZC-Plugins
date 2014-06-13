<?php

namespace pemapmodder\antixray;

use pocketmine\block\Block;
use pocketmine\event\entity\EntityMoveEvent;
use pocketmine\event\Listener;
use pocketmine\event\server\DataPacketSendEvent;
use pocketmine\network\protocol\ChunkDataPacket;
use pocketmine\plugin\PluginBase;

class Main extends PluginBase implements Listener{
	public function onEnable(){
		$this->getServer()->getPluginManager()->registerEvents($this, $this);
	}
	public function onPacketSend(DataPacketSendEvent $event){
		$pk = $event->getPacket();
		if($pk instanceof ChunkDataPacket){
			$this->replaceChunkBlocks([
				Block::COAL_ORE,
				Block::DIAMOND_ORE,
				Block::GLOWING_REDSTONE_ORE,
				Block::GOLD_ORE,
				Block::IRON_ORE,
				Block::LAPIS_ORE,
				Block::LIT_REDSTONE_ORE,
				Block::REDSTONE_ORE
			], [0, 0, 0, 0, 0, 0, 0, 0], $pk->data);
		}
	}
	public function replaceChunkBlocks(array $ids, array $replaceAs, &$ordered){

	}
}
// SO HARD! I GIVE UP!
