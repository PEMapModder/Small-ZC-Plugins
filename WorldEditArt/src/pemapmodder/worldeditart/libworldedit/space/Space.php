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

interface Space{
	/**
	 * @param BlockSet $blocks
	 * @param bool $update
	 * @return WorldModification
	 */
	public function setAll(BlockSet $blocks, $update);
	/**
	 * @param Block[] $from
	 * @param BlockSet $to
	 * @param bool $update
	 * @return WorldModification
	 */
	public function replaceAll($from, BlockSet $to, $update);
	/**
	 * @param BlockSet $blocks
	 * @param bool $update
	 * @return WorldModification
	 */
	public function setMargin(BlockSet $blocks, $update);
	/**
	 * @param Block[] $from
	 * @param BlockSet $to
	 * @param bool $update
	 * @return WorldModification
	 */
	public function replaceMargin($from, BlockSet $to, $update);
	/**
	 * @param BlockSet $blocks
	 * @param $update
	 * @return bool
	 */
	public function setContent(BlockSet $blocks, $update);
	/**
	 * @param BlockSet $blocks
	 * @param bool $update
	 * @return WorldModification
	 */
	public function replaceContent(BlockSet $blocks, $update);
}
