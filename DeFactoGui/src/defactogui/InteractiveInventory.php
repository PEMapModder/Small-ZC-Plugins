<?php

/**
 * DeFactoGui
 * Copyright (C) 2015 PEMapModder
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License along
 * with this program; if not, write to the Free Software Foundation, Inc.,
 * 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301 USA.
 */

namespace defactogui;

use pocketmine\inventory\CustomInventory;
use pocketmine\inventory\InventoryType;
use pocketmine\item\Item;
use pocketmine\level\Level;
use pocketmine\network\Network;
use pocketmine\network\protocol\TileEventPacket;
use pocketmine\Player;
use pocketmine\Server;

class InteractiveInventory extends CustomInventory{
	private $main;
	private $buttons;
	/**
	 * @param DeFactoGui $main
	 * @param Button[] $buttons - list of buttons to display in the chest.
	 * @param int $size default 127 - size of the inventory.
	 * @param string|null $title - unused.
	 */
	public function __construct(DeFactoGui $main, $buttons = [], $size = 127, $title = null){
		$this->main = $main;
		$this->buttons = $buttons;
		if($size < count($buttons)){
			throw new \InvalidArgumentException("InteractiveInventory size is less than count of buttons passed");
		}
		/** @var Item[] $items */
		$items = array_map(function(Button $button){
			return $button->onLoad($this);
		}, $buttons);
		parent::__construct($main->getFakeTile(), InventoryType::get(InventoryType::CHEST), $items, $size, $title);
	}

	public function onOpen(Player $who){
		parent::onOpen($who);
		if(count($this->getViewers()) === 1){
			$pk = new TileEventPacket();
			$pk->x = $this->main->getFakeTile()->x;
			$pk->y = $this->main->getFakeTile()->y;
			$pk->z = $this->main->getFakeTile()->z;
			$pk->case1 = 1;
			$pk->case2 = 2;
			if(($level = $this->main->getFakeTile()->getLevel()) instanceof Level){
				Server::broadcastPacket($level->getChunkPlayers($this->main->getFakeTile()->x >> 4, $this->main->getFakeTile()->z >> 4), $pk->setChannel(Network::CHANNEL_WORLD_EVENTS));
			}
		}
	}
	public function onClose(Player $who){
		if(count($this->getViewers()) === 1){
			$pk = new TileEventPacket();
			$pk->x = $this->main->getFakeTile()->x;
			$pk->y = $this->main->getFakeTile()->y;
			$pk->z = $this->main->getFakeTile()->z;
			$pk->case1 = 1;
			$pk->case2 = 0;
			if(($level = $this->main->getFakeTile()->getLevel()) instanceof Level){
				Server::broadcastPacket($level->getChunkPlayers($this->main->getFakeTile()->x >> 4, $this->main->getFakeTile()->z >> 4), $pk->setChannel(Network::CHANNEL_WORLD_EVENTS));
			}
		}
		parent::onClose($who);
	}
}
