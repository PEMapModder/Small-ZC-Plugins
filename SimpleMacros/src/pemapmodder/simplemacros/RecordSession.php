<?php

/*
 * SimpleMacros
 * Copyright (C) 2015-2017 PEMapModder and contributors
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along
 * with this program; if not, write to the Free Software Foundation, Inc.,
 * 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301 USA.
 */

namespace pemapmodder\simplemacros;

class RecordSession{
	public $stack = [];
	public $paused;
	public $tee;
	public $sprintf;

	/**
	 * RecordSession constructor.
	 *
	 * @param bool $tee whether the command should be executed (true) or cancelled (false)
	 * @param bool $sprintf
	 */
	public function __construct(bool $tee, bool $sprintf){
		$this->paused = false;
		$this->tee = $tee;
		$this->sprintf = $sprintf;
	}

	public function handle(string $line) : bool{
		if($this->paused){
			return false;
		}
		$this->stack[] = $line;
		// TODO add sprintf testing
		return !$this->tee;
	}

	public function save(Main $main, string $name, string $author) : bool{
		return file_put_contents($main->getDataFolder() . "macros/$name.txt",
				"# Created " . date(DATE_ISO8601) . " by $author\n" .
				($this->sprintf ? "#sprintf enabled\n" : "") .
				implode("\n", $this->stack)
			) != false;
	}
}
