<?php

namespace pemapmodder\bulkcommands;

class BulkCommandSession{
	public $format = null;
	public $cnt = 0;
	public function format($message){
		return sprintf($this->format, ...array_fill(0, $this->cnt, $message));
	}
}
