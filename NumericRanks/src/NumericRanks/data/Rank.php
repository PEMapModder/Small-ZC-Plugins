<?php

namespace NumericRanks\data;

use NumericRanks\NumericRanks;

class Rank
{
	public function __construct(NumericRanks $plugin, $rankName)
	{
		$this->plugin = $plugin;
		$this->rankName = $rankName;
	}
	
	public function getData()
	{
		return $this->plugin->getConfig()->getNested("ranks." . $this->rankName);
	}
	
	public function getName()
	{
		return $this->rankName;
	}
	
	public function getRankIndex()
	{
		return $this->getData()["index"];
	}
	
	public function isDefault()
	{
		$result = isset($this->getData()["defaultRank"]) and $this->getData()["defaultRank"] == true;
		
		return $result;
	}
}