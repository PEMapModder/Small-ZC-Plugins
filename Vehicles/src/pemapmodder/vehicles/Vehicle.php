<?php

namespace pemapmodder\vehicles;

use pocketmine\Player;
use pocketmine\level\Position;
use pocketmine\network\protocol\UpdateBlockPacket;

abstract class Vehicle{
	/**
	 * @var
	 */
	private $lastPos = null;
	/**
	 * @var Player
	 */
	private $driver;
	/**
	 * @var Player[]
	 */
	private $viewers = [];
	/**
	 * @param string[] $args
	 * @param Player $driver
	 * @param Player[]|bool $viewers
	 * @throws InvalidArgumentsException
	 */
	public function __construct(array $args, Player $driver, $viewers = false){
		$this->driver = $driver;
		$this->viewers = is_array($viewers) ? [$driver]:$viewers;
		try{
			$this->init($args);
		}catch(InvalidArgumentsException $e){
			throw $e;
		}
	}
	public abstract function init(array $args);
	public function getViewers(){
		return $this->viewers;
	}
	public function setViewers(array $viewers = []){
		$this->viewers = $viewers;
	}
	/**
	 * @return VehicleScale
	 */
	public abstract function getScale();
	/**
	 * @return VehicleShape
	 */
	public abstract function getShape(); // TODO transformable shape // is this too aspiring?
	public function locomote(Position $pos){
		if($this->lastPos instanceof Position){
			$this->destructAt($this->lastPos);
		}
		$this->lastPos = $pos;
		$this->constructAt($pos);
	}
	public function destruct(){
		$this->destructAt($this->lastPos);
	}
	public function construct(){
		$this->constructAt($this->lastPos);
	}
	public function constructAt(Position $pos){
		$changes = $this->getShape()->getMapAt($pos);
		foreach($changes as $change){
			$pk = new UpdateBlockPacket;
			$pk->x = $change->getX();
			$pk->y = $change->getY();
			$pk->z = $change->getZ();
			$pk->block = $change->getID();
			$pk->meta = $change->getDamage();
			foreach($this->viewers as $viewer){
				$viewer->dataPacket($pk);
			}
		}
	}
	public function destructAt(Position $pos){
		$changes = $this->getShape()->getMapAt($pos->subtract($this->getScale()->get("x-"), $this->getScale()->get("y-", $this->getScale()->get("z-"))));
		foreach($changes as $change){
			$pk = new UpdateBlockPacket;
			$pk->x = $change->getX();
			$pk->y = $change->getY();
			$pk->z = $change->getZ();
			$pk->block = 0;
			$pk->meta = 0;
			foreach($this->viewers as $viewer){
				$viewer->dataPacket($pk);
			}
		}
	}
}
