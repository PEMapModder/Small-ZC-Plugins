<?php

namespace votifierpe;

use pocketmine\event\Cancellable;
use pocketmine\event\Event;

class VotifierPEVoteEvent extends Event implements Cancellable{
	public static $handlerList = null;
	public static $eventPool = [];
	public static $nextEvent = 0;
	private $vote;
	public function __construct(array $vote){
		$this->vote = $vote;
	}
	public function getVote(){
		return $this->vote;
	}
}
