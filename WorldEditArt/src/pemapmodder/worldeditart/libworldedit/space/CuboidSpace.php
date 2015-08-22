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

namespace pemapmodder\worldeditart\libworldedit\space;

use pemapmodder\worldeditart\libworldedit\BlockSet;
use pemapmodder\worldeditart\libworldedit\WorldModification;
use pocketmine\block\Block;

class CuboidSpace implements Space{
	/**
	 * @param BlockSet $blocks
	 * @param bool $update
	 * @return WorldModification
	 */
	public function setAll(BlockSet $blocks, $update){
		// TODO: Implement setAll() method.
	}
	/**
	 * @param Block[] $from
	 * @param BlockSet $to
	 * @param bool $update
	 * @return WorldModification
	 */
	public function replaceAll($from, BlockSet $to, $update){
		// TODO: Implement replaceAll() method.
	}
	/**
	 * @param BlockSet $blocks
	 * @param bool $update
	 * @return WorldModification
	 */
	public function setMargin(BlockSet $blocks, $update){
		// TODO: Implement setMargin() method.
	}
	/**
	 * @param Block[] $from
	 * @param BlockSet $to
	 * @param bool $update
	 * @return WorldModification
	 */
	public function replaceMargin($from, BlockSet $to, $update){
		// TODO: Implement replaceMargin() method.
	}
}
