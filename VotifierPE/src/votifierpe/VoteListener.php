<?php

namespace votifierpe;

interface VoteListener{
	public function __construct(VotifierPE $plugin);

	/**
	 * @param array $vote an associative array with keys "service", "username", "address" and "timestamp"
	 *
	 * @return bool|null
	 */
	public function listen(array $vote);
}
