<?php

namespace customareas\db;

use customareas\Area;
uae customareas\CustomAreas;

interface Database{
	public function nextId();
	public function addArea(Area $area);
	public function deleteArea($id);
}
