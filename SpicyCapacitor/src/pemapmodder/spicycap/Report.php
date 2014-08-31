<?php

namespace pemapmodder\spicycap;

class Report{
	const FLAG_RESOLVED = 0b0001;
	/** @var \pemapmodder\spicycap\SpicyCap */
	private $main;
	/** @var int */
	private $id;
	/** @var string */
	private $fromName;
	/** @var string */
	private $fromIP;
	/** @var string */
	private $toName;
	/** @var string */
	private $toIP;
	/** @var int */
	private $flags;
	/** @var string */
	private $description;
	/** @var null|string */
	private $assignee;
	/** @var array */
	private $logs;
	/**
	 * @param SpicyCap $main
	 * @param int $id
	 * @param string $fromName
	 * @param string $fromIP
	 * @param string $toName
	 * @param string $toIP
	 * @param int $flags
	 * @param string $description
	 * @param string|null $assignee
	 * @param string $logs
	 */
	public function __construct(SpicyCap $main, $id, $fromName, $fromIP, $toName, $toIP, $flags, $description, $assignee, $logs){
		$this->main = $main;
		$this->id = $id;
		$this->fromName = $fromName;
		$this->fromIP = $fromIP;
		$this->toName = $toName;
		$this->toIP = $toIP;
		$this->flags = $flags;
		$this->description = $description;
		$this->assignee = $assignee;
		$this->logs = json_decode(gzdecode($logs));
	}
	public function isResolved(){
		return ($this->flags & self::FLAG_RESOLVED) === self::FLAG_RESOLVED;
	}
	public function setResolved($resolved = true){
		if($resolved){
			$this->flags |= self::FLAG_RESOLVED;
		}
		else{
			$this->flags &= ~self::FLAG_RESOLVED;
		}
	}
	/**
	 * @return \pemapmodder\spicycap\SpicyCap
	 */
	public function getMain(){
		return $this->main;
	}
	/**
	 * @return int
	 */
	public function getID(){
		return $this->id;
	}
	/**
	 * @return string
	 */
	public function getFromName(){
		return $this->fromName;
	}
	/**
	 * @return string
	 */
	public function getFromIP(){
		return $this->fromIP;
	}
	/**
	 * @return string
	 */
	public function getToName(){
		return $this->toName;
	}
	/**
	 * @return string
	 */
	public function getToIP(){
		return $this->toIP;
	}
	/**
	 * @return int
	 */
	public function getFlags(){
		return $this->flags;
	}
	/**
	 * @return string
	 */
	public function getDescription(){
		return $this->description;
	}
	/**
	 * @return null|string
	 */
	public function getAssignee(){
		return $this->assignee;
	}
	public function compressLogs(){
		return gzencode(json_encode($this->logs));
	}
}
