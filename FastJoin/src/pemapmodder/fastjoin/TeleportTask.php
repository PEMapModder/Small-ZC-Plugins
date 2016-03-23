<?php

namespace pemapmodder\fastjoin;

use pocketmine\level\Position;
use pocketmine\Player;
use pocketmine\scheduler\PluginTask;

class TeleportTask extends PluginTask{
	/** @var Player */
	private $player;
	/** @var Position */
	private $pos;

	public function __construct(FastJoin $owner, Player $player, Position $pos){
		parent::__construct($owner);
		$this->player = $player;
		$this->pos = $pos;
	}

	public function onRun($t){
		$this->player->teleport($this->pos);
	}
}
