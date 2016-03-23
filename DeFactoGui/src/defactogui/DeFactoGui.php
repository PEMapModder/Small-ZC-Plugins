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

use pocketmine\nbt\NBT;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\ListTag;
use pocketmine\nbt\tag\IntTag;
use pocketmine\nbt\tag\StringTag;
use pocketmine\plugin\PluginBase;
use pocketmine\tile\Chest;
use pocketmine\tile\Tile;

class DeFactoGui extends PluginBase{
	/** @var Button[] */
	private $registeredButtons = [];
	/** @var Chest */
	private $fakeTile;

	public function onEnable(){
		$lv = $this->getServer()->getDefaultLevel();
		$spawn = $lv->getSpawnLocation()->floor();
		$chunk = $lv->getChunk($spawn->x >> 4, $spawn->z >> 4);
		$nbt = new CompoundTag;
		$nbt->Items = new ListTag("Items", []);
		$nbt->Items->setTagType(NBT::TAG_List);
		$nbt->id = new StringTag("id", Tile::CHEST);
		$nbt->x = new IntTag("x", $spawn->x);
		$nbt->y = new IntTag("y", $spawn->y);
		$nbt->z = new IntTag("z", $spawn->z);
		$this->fakeTile = new Chest($chunk, $nbt);
	}

	public function registerButton(Button $button){
		$this->registeredButtons[get_class($button)] = $button;
	}

	/**
	 * @return Chest
	 */
	public function getFakeTile(){
		return $this->fakeTile;
	}
}
