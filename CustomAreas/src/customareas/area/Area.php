<?php

namespace customareas\area;

use customareas\shape\Shape;

class Area{
	const TYPE_OWNER = 2;
	const TYPE_USER = 1;
	const TYPE_NON_USER = 0;

	const FLAG_PLACE = 0b0000000000000001; // default true
	const FLAG_BREAK = 0b0000000000000010; // default true
	const FLAG_IGNITE_TNT = 0b0000000000100000; // default true
	const FLAG_PLACE_LIQUID_OR_FIRE = 0b0000000010000000; // default true
	const FLAG_EDIT = 0b0000000010100011; // default true
	const FLAG_OPEN_CHEST = 0b0000000000000100; // default true
	const FLAG_OPEN_FURNACE = 0b0000000000001000; // default true
	const FLAG_OPEN = 0b0000000000001100; // default true
	const FLAG_MISC_INTERACT = 0b0000000001000000; // default true
	const FLAG_TOUCH = 0b0000000011111111; // default true
	// remember, these are IMMUNE TO flags!
	const FLAG_DAMAGED_BY_PLAYER = 0b0000000100000000; // default false
	const FLAG_DAMAGED_BY_MOB = 0b0000001000000000; // default false
	const FLAG_DAMAGED_BY_ENTITY = 0b0000001100000000; // default false
	const FLAG_DAMAGED_BY_FIRE = 0b0000010000000000; // default false
	const FLAG_DAMAGED_BY_FALL = 0b0000100000000000; // default false
	const FLAG_DAMAGED_BY_DROWN = 0b0001000000000000; // default false
	const FLAG_DAMAGED_BY_SUFFOCATE = 0b0010000000000000; // default false
	const FLAG_DAMAGED_BY_VOID = 0b0100000000000000; // default false
	const FLAG_DAMAGED_BY_EXPLOSION = 0b1000000000000000; // default false
	const FLAG_DAMAGED = 0b1111111100000000; // default false

	const FLAG_DAMAGE_PLAYER = 0b0000000000000001 << 16; // default true
	const FLAG_DAMAGE_MOB = 0b0000000000000010 << 16; // default true
	const FLAG_DAMAGE = 0b0000000000000011 << 16; // default true
	const FLAG_ENTRY = 0b0000000100000000 << 16; // default true
	const FLAG_EDIT_FLAGS = 0b1000000000000000 << 16; // default false
	const FLAG_ALL = 0xFFFFFFFF;

	/** @var string */
	private $name;
	/** @var Shape|null */
	private $shape;
	/** @var int */
	private $userFlags, $nonUserFlags;
	/** @var string */
	private $owner;
	/** @var string[] */
	private $users = [];

	/**
	 * @param string     $name
	 * @param Shape|null $shape
	 * @param int        $userFlags
	 * @param int        $nonUserFlags
	 * @param string     $owner
	 * @param string[]   $users default {@code []}
	 */
	public function __construct($name, $shape, $userFlags, $nonUserFlags, $owner, $users = []){
		if(strpos($name, "\0") !== false){
			throw new \InvalidArgumentException("Area names must not contain the null byte"); // hacker!
		}
		$this->name = $name;
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

	/**
	 * @return string
	 */
	public function getName(){
		return $this->name;
	}

	/**
	 * @return Shape|null
	 */
	public function getShape(){
		return $this->shape;
	}

	/**
	 * @param Shape|null $shape
	 */
	public function setShape($shape){
		$this->shape = $shape;
	}

	/**
	 * @return int
	 */
	public function getUserFlags(){
		return $this->userFlags;
	}

	/**
	 * @param int $userFlags
	 */
	public function setUserFlags($userFlags){
		$this->userFlags = $userFlags;
	}

	/**
	 * @return int
	 */
	public function getNonUserFlags(){
		return $this->nonUserFlags;
	}

	/**
	 * @param int $nonUserFlags
	 */
	public function setNonUserFlags($nonUserFlags){
		$this->nonUserFlags = $nonUserFlags;
	}

	/**
	 * @return string
	 */
	public function getOwner(){
		return $this->owner;
	}

	/**
	 * @return string[]
	 */
	public function getUsers(){
		return $this->users;
	}

	/**
	 * @param string[] $users
	 */
	public function setUsers($users){
		$this->users = $users;
	}

	public function addUser($user){
		if($this->hasUser($user)){
			throw new \RuntimeException("Such user already exists");
		}
		$this->users[] = $user;
	}

	public function rmUser($user){
		$key = array_search($user, $this->users);
		if($key !== false){
			throw new \RuntimeException("Such user doesn't exist");
		}
		unset($this->users[$key]);
	}

	public function hasUser($user){
		return in_array($user, $this->users);
	}
}
