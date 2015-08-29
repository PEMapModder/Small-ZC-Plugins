<?php

/*
 * Small-ZC-Plugins
 *
 * Copyright (C) 2015 PEMapModder and contributors
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * @author PEMapModder
 */

namespace pemapmodder\mist;

use pocketmine\entity\Effect;
use pocketmine\entity\Entity;
use pocketmine\network\protocol\AddPlayerPacket;
use pocketmine\network\protocol\MobEffectPacket;
use pocketmine\network\protocol\SetEntityDataPacket;
use pocketmine\Player;
use pocketmine\utils\Random;

class MistySpecter{
	/** @var Player */
	private $player;
	private $username;
	private $eid;
	private $random;
	private $skin;
	private $slim;
	public function __construct(Player $player, $username, $skin, $slim){
		$this->player = $player;
		$this->username = $username;
		$this->eid = Entity::$entityCount; // use normal entity IDs so that mods won't be able to identify specters and hide them
		$this->random = new Random();
		$this->skin = $skin;
		$this->slim = $slim;
		$this->spawn();
		$this->invis();
	}
	private function spawn(){
		$pk = new AddPlayerPacket;
		$pk->clientID = $this->eid;
		$pk->username = $this->username;
		$pk->eid = $this->eid;
		$pk->x = $this->player->x + $this->random->nextSignedFloat() * 5;
		$pk->y = $this->player->y + $this->random->nextSignedFloat() * 5;
		$pk->z = $this->player->z + $this->random->nextSignedFloat() * 5;
		$pk->speedX = 0.0;
		$pk->speedY = 0.0;
		$pk->speedZ = 0.0;
		$pk->yaw = $this->random->nextSignedFloat() * 180;
		$pk->pitch = $this->random->nextSignedFloat() * 90;
		$pk->item = 0;
		$pk->meta = 0;
		$pk->metadata = [
			Entity::DATA_FLAGS => [Entity::DATA_TYPE_BYTE, 0],
			Entity::DATA_AIR => [Entity::DATA_TYPE_SHORT, 300],
			Entity::DATA_NAMETAG => [Entity::DATA_TYPE_STRING, $this->username],
			Entity::DATA_SHOW_NAMETAG => [Entity::DATA_TYPE_BYTE, 1],
			Entity::DATA_SILENT => [Entity::DATA_TYPE_BYTE, 0],
			Entity::DATA_NO_AI => [Entity::DATA_TYPE_BYTE, 0],
		];
		$pk->slim = $this->slim;
		$pk->skin = $this->skin;
		$this->player->dataPacket($pk);
	}
	private function invis(){
		$pk = new SetEntityDataPacket;
		$pk->eid = $this->eid;
		$flags = 1 << Entity::DATA_FLAG_INVISIBLE;
		$pk->data = [Entity::DATA_FLAGS => [Entity::DATA_TYPE_INT, $flags]];
		$this->player->dataPacket($pk);
		$pk = new SetEntityDataPacket;
		$pk->eid = $this->eid;
		$pk->data = [Entity::DATA_SHOW_NAMETAG => [Entity::DATA_TYPE_BYTE, 0]];
		$this->player->dataPacket($pk);
	}
}
