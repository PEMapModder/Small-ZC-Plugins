<?php

namespace pemapmodder\fastjoin;

use pocketmine\level\format\FullChunk;
use pocketmine\level\format\LevelProvider;
use pocketmine\level\Level;
use pocketmine\math\Vector3;
use pocketmine\utils\Binary;

class FlatProvider implements LevelProvider{
	/** @var Level */
	private $level;
	/** @var string */
	private $path;
	private $dummyChunk;

	public function __construct(Level $level, $path){
		$this->level = $level;
		$this->path = $path;
		$this->dummyChunk = str_repeat("\x07\x09" . str_repeat("\x00", 126), 256) . str_repeat("\x00", 16384) . str_repeat("\xFF", 16384) . str_repeat("\xFF", 16384) . str_repeat("\x01", 256) . str_repeat("\x01\x64\xFF\x00", 256);
	}

	public static function getProviderName(){
		return "DummyFlat";
	}

	public static function getProviderOrder(){
		return self::ORDER_ZXY;
	}

	public static function usesChunkSection(){
		return false;
	}

	public function requestChunkTask($x, $z){
		$this->getLevel()->chunkRequestCallback($x, $z, zlib_encode(Binary::writeLInt($x) . Binary::writeLInt($z) . $this->dummyChunk, ZLIB_ENCODING_DEFLATE, Level::$COMPRESSION_LEVEL));
	}

	public function getPath(){
		return $this->path;
	}

	public static function isValid($path){
		return is_file($path . "/.fastjoin");
	}

	public static function generate($path, $name, $seed, $generator, array $options = []){
		$path = rtrim(realpath($path), "/\\") . DIRECTORY_SEPARATOR;
		if(!is_dir($path)){
			mkdir($path, 0777, true);
		}
		touch($path . ".fastjoin");
	}

	public function getGenerator(){
		return "FastJoin";
	}

	public function getGeneratorOptions(){
		return [];
	}

	public function getChunk($X, $Z, $create = false){
		return null;
	}

	public static function createChunkSection($Y){
		return null;
	}

	public function saveChunks(){
	}

	public function saveChunk($X, $Z){
	}

	public function unloadChunks(){
	}

	public function loadChunk($X, $Z, $create = false){
		return true;
	}

	public function unloadChunk($X, $Z, $safe = true){
		return true;
	}

	public function isChunkGenerated($X, $Z){
		return true;
	}

	public function isChunkPopulated($X, $Z){
		return true;
	}

	public function isChunkLoaded($X, $Z){
		return true;
	}

	public function setChunk($chunkX, $chunkZ, FullChunk $chunk){
	}

	public function getName(){
		return trim(basename($this->path), "/\\");
	}

	public function getTime(){
		return time();
	}

	public function setTime($value){
	}

	public function getSeed(){
		return 0;
	}

	public function setSeed($value){
	}

	public function getSpawn(){
		return new Vector3(0, 128, 0);
	}

	public function setSpawn(Vector3 $pos){
	}

	public function getLoadedChunks(){
	}

	public function getLevel(){
		return $this->level;
	}

	public function close(){
	}
}
