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

use pocketmine\item\Item;
use pocketmine\Player;

interface Button{
	const NONEXIST = -1;

	/**
	 * @param InteractiveInventory $inventory - of which inventory is the button moved in.
	 * @param int                  $from      - from which inventory slot,
	 *                                        or {@link #NONEXIST NONEXIST} if it wasn't in the inventory before.
	 * @param int                  $to        - to which inventory slot,
	 *                                        or {@link #NONEXIST NONEXIST} if it is removed.
	 * @param bool                 $force     default false - if true, the button must not return false
	 *
	 * @return bool - allow moving or not
	 */
	public function onMove(InteractiveInventory $inventory, $from, $to, $force = false);

	/**
	 * @param InteractiveInventory $inventory - of which inventory is the button loaded in.
	 *
	 * @return Item - the item icon for the button to display as in the chest.
	 */
	public function onLoad(InteractiveInventory $inventory);

	/**
	 * @param InteractiveInventory $inventory - of which inventory is the button clicked in.
	 * @param Player               $player    - the player who clicked the button
	 *
	 * @return void
	 */
	public function onClick(InteractiveInventory $inventory, Player $player);
}
