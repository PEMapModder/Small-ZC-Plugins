<?php

namespace pemapmodder\worldeditart\utils\macro;

use pemapmodder\worldeditart\Main;
use pocketmine\block\Block;
use pocketmine\level\Position;
use pocketmine\scheduler\ServerScheduler;

class Macro{
	const COMPRESSION_NONE = 0;
	const COMPRESSION_GZIP = ZLIB_ENCODING_GZIP;
	// general
	/** @var bool */
	private $appendable;
	/** @var string */
	private $author;
	/** @var string */
	private $description;
	// changeable-only-when-appendable fields
	/** @var MacroOperation[] */
	private $ops;
	/** @var bool */
	private $hibernating = false;
	/** @var Position */
	private $anchor;
	/** @var int */
	private $compression = ZLIB_ENCODING_GZIP;
	/**
	 * @param bool $isAppendable
	 * @param Position|MacroOperation[] $mainArg
	 * @param string $author
	 * @param string $description
	 */
	public function __construct($isAppendable, $mainArg, $author, $description = ""){
		$this->appendable = $isAppendable;
		$this->author = $author;
		$this->description = $description;
		if($isAppendable){
			$this->ops = [];
			$this->anchor = $mainArg;
		}
		else{
			$this->ops = $mainArg;
		}
	}
	/**
	 * @param MacroOperation $operation
	 * @throws \BadMethodCallException
	 */
	public function append(MacroOperation $operation){
		if(!$this->isAppendable()){
			throw new \BadMethodCallException("Trying to append to non-appendable macro");
		}
		$this->ops[] = $operation;
	}
	/**
	 * @param bool $hibernating
	 * @throws \BadMethodCallException
	 */
	public function setHibernating($hibernating){
		if(!$this->isAppendable()){
			throw new \BadMethodCallException("Trying to set hibernating mode of non-appendable macro");
		}
		$this->hibernating = $hibernating;
	}
	/**
	 * @return bool
	 * @throws \BadMethodCallException
	 */
	public function getHibernating(){
		if(!$this->isAppendable()){
			throw new \BadMethodCallException("Trying to get hibernating mode of non-appendable macro");
		}
		return $this->hibernating;
	}
	/**
	 * @return bool
	 * @throws \BadMethodCallException
	 */
	public function isHibernating(){
		if(!$this->isAppendable()){
			throw new \BadMethodCallException("Trying to get hibernating mode of non-appendable macro");
		}
		return $this->hibernating;
	}
	public function getAnchor(){
		if(!$this->isAppendable()){
			throw new \BadMethodCallException("Trying to get anchor of non-appendable macro");
		}
		return $this->anchor;
	}
	public function execute(ServerScheduler $scheduler, Position $anchor, Main $main){
		$ticks = 0;
		foreach($this->ops as $op){
			if($op->getType() === MacroOperation::TYPE_WAIT){
				$ticks += $op->getLength();
				continue;
			}
			$anchor->level->acquire(); // StrongRef
			$scheduler->scheduleDelayedTask(new MacroOperationTask($main, $op, $anchor), $ticks);
		}
	}
	/**
	 * @return string
	 */
	public function getAuthor(){
		return $this->author;
	}
	/**
	 * @return string
	 */
	public function getDescription(){
		return $this->description;
	}
	/**
	 * @return bool
	 */
	public function isAppendable(){
		return $this->appendable;
	}
	/**
	 * @return MacroOperation[]
	 */
	public function getOperations(){
		return $this->ops;
	}
	public function addBlock(Block $block){
		$this->append(new MacroOperation($block->subtract($this->getAnchor()), $block));
	}
	public function wait($ticks){
		$this->append(new MacroOperation($ticks));
	}
	/**
	 * @return bool|int
	 */
	public function getCompression(){
		return $this->compression;
	}
	/**
	 * @param bool|int $compression
	 */
	public function setCompression($compression){
		$this->compression = $compression;
	}
}
