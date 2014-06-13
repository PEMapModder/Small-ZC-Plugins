<?php

namespace pemapmodder\pocketenchant;

use pocketmine\level\Position;
use pocketmine\Server;
use pocketmine\utils\Binary;

class Database{
	private $main;
	/** @var Position[][] */
	private $tables;
	private $items;
	public function __construct(Main $main){
		$this->main = $main;
		$res = fopen($this->main->getDataFolder()."tables.dat", "r");
		$count = Binary::readLong(fread($res, 8));
		$del = 0;
		$tables = [];
		for($i = 0; $i < $count; $i++){
			$pos = $this->readPosition($res);
			if($pos instanceof Position){
				$world = $pos->getLevel()->getName();
				if(!isset($tables[$world])){
					$tables[$world] = [];
				}
				$tables[$world][] = $pos;
			}
			else{
				$del++;
			}
		}
		fclose($res);
		$this->tables = $tables;
		$res = fopen($this->main->getDataFolder()."items.dat", "r");
		$count = Binary::readLong(fread($res, 8));
		$enchantments = [];
		for($i = 0; $i < $count; $i++){
			$e = $this->readEnchantment($res);
			if(!isset($enchantments[$e["player"]])){
				$enchantments[$e["player"]] = [];
			}
		}
		fclose($res);
		if($del > 0){
			$this->main->getLogger()->notice("$del enchanting tables deleted due to level corruption/deletion.");
		}
	}
	public function save(){
		$res = fopen($this->main->getDataFolder()."tables.dat", "w");
		fwrite($res, Binary::writeLong(count($this->tables)));
		/** @var Position[] $tables */
		$tables = call_user_func_array("array_merge", $this->tables);
		foreach($tables as $table){
			$this->writePosition($res, $table);
		}
		fclose($res);
	}
	/**
	 * @param resource $res
	 * @return bool|Position
	 */
	public function readPosition($res){
		$x = Binary::readInt(fread($res, 4)) * 16;
		$z = Binary::readInt(fread($res, 4)) * 16;
		$xz = ord(fread($res, 1));
		$z += ($xz & 0x0F);
		$xz >>= 4;
		$x += ($xz & 0x0F);
		$Y = fread($res, 2);
		$y = ord($Y{0}) * 0xFF + ord($Y{1});
		$world = fread($res, ord(fread($res, 1)));
		if(!Server::getInstance()->isLevelLoaded($world)){
			if(!Server::getInstance()->isLevelGenerated($world)){
				return false;
			}
			$success = Server::getInstance()->loadLevel($world);
			if(!$success){
				return false;
			}
		}
		$level = Server::getInstance()->getLevel($world);
		return new Position($x, $y, $z, $level);
	}
	public function writePosition($res, Position $pos){
		fwrite($res, Binary::writeInt((int) $pos->getFloorX() / 16));
		fwrite($res, Binary::writeInt((int) $pos->getFloorZ() / 16));
		fwrite($res, chr(($pos->getFloorX() & 0x0F) << 4 + ($pos->getFloorZ() & 0x0F)));
		fwrite($res, chr(((int) $pos->getFloorY() / 256)));
		fwrite($res, chr($pos->getFloorY() & 0xFF));
		fwrite($res, chr(strlen($pos->getLevel()->getName())));
		fwrite($res, $pos->getLevel()->getName());
	}
	public function readEnchantment($res){
		$data = [];
		$length = ord(fread($res, 1));
		if($length === 0){
			$data["container"] = $this->readPosition($res);
		}
		else{
			$data["container"] = fread($res, $length);
		}
		$data["slot"] = ord(fread($res, 1)); // armor pieces also in player inventory
		$data["meta"] = ord(fread($res, 1)) * 0x100 + ord(fread($res, 1));
	}
	public function writeEnchantment($res, array $data){
	}
}
