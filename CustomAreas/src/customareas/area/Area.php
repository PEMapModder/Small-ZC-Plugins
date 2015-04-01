<?php

namespace customareas\area;

use customareas\shape\Shape;

class Area{
	const TYPE_OWNER = 2;
	const TYPE_USER = 1;
	const TYPE_NON_USER = 0;

	const FLAG_PLACE             = 0b0000000000000001;
	const FLAG_BREAK             = 0b0000000000000010;
	const FLAG_IGNITE_TNT        = 0b0000000000100000;
	const FLAG_SPREAD            = 0b0000000001000000;
	const FLAG_EDIT              = 0b0000000001100011;
	const FLAG_OPEN_CHEST        = 0b0000000000000100;
	const FLAG_OPEN_FURNACE      = 0b0000000000001000;
	const FLAG_OPEN              = 0b0000000000001100;
	const FLAG_TOUCH             = 0b0000000011111111;
	const FLAG_DAMAGED_BY_NATURE = 0b0000000100000000;
	const FLAG_DAMAGED_BY_MOB    = 0b0000001000000000;
	const FLAG_DAMAGED_BY_PLAYER = 0b0000010000000000;
	const FLAG_DAMAGED           = 0b0000111100000000;
	const FLAG_DAMAGE_PLAYER     = 0b0001000000000000;
	const FLAG_DAMAGE_MOB        = 0b0010000000000000;
	const FLAG_DAMAGE            = 0b0111000000000000;
	const FLAG_EDIT_FLAGS        = 0b1000000000000000;
	const FLAG_ALL               = 0b1111111111111111;

	/** @var Shape|null */
	private $shape;
	/** @var int */
	private $userFlags, $nonUserFlags;
	/** @var string */
	private $owner;
	/** @var string[] */
	private $users = [];
	/**
	 * @param Shape|null $shape
	 * @param int $userFlags
	 * @param int $nonUserFlags
	 * @param string $owner
	 * @param string[] $users default {@code []}
	 */
	public function __construct($shape, $userFlags, $nonUserFlags, $owner, $users = []){
		$this->shape = $shape;
		$this->userFlags = $userFlags;
		$this->nonUserFlags = $nonUserFlags;
		$this->owner = $owner;
		$this->users = $users;
	}
	public function getUserType($name){
		$name = strtolower($name);
		if($name === $this->owner){
			return self::TYPE_OWNER;
		}
		return in_array($name, $this->users) ? self::TYPE_USER : self::TYPE_NON_USER;
	}
	public function getFlagsOf($name){
		$type = $this->getUserType($name);
		if($type === self::TYPE_OWNER){
			return self::FLAG_ALL;
		}
		return $type === self::TYPE_USER ? $this->userFlags : $this->nonUserFlags;
	}
	public function hasFlag($name, $flag, $acceptPartial = true){
		$and = $flag & $this->getFlagsOf($name);
		return $acceptPartial ? ($and > 0) : ($and === $flag);
	}
}
