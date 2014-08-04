<?php

namespace pemapmodder\worldeditart\utils\provider\clip;

use pemapmodder\worldeditart\Main;
use pemapmodder\worldeditart\utils\clip\Clip;
use pemapmodder\worldeditart\utils\StringReader;
use pemapmodder\worldeditart\utils\StringWriter;
use pocketmine\block\Block;
use pocketmine\math\Vector3;

class BinaryClipboardProvider extends ClipboardProvider{
	/** @var Clip[] */
	private $caches = [];
	public function __construct(Main $main, $args){
		parent::__construct($main);
		$this->path = $main->getDataFolder().$args["path"];
	}
	public function getPath($name){
		return str_replace("<name>", strtolower($name), $this->path);
	}
	public function offsetExists($name){
		return is_file($this->getPath($name));
	}
	public function offsetGet($name){
		if(isset($this->caches[$name])){
			return $this->caches[$name];
		}
		$reader = StringReader::fromFile($this->getPath($name));
		$clip = self::parse($reader);
		$this->caches[$name] = $clip;
		return $clip;
	}
	public static function parse(StringReader $reader){
		$name = $reader->readBString();
		$length = $reader->readLong(false);
		$blocks = [];
		for($i = 0; $i < $length; $i++){
			if($reader->feof()){
				throw new \Exception("Unexpected end of file");
			}
			$x = $reader->readInt();
			$y = $reader->readShort();
			$z = $reader->readInt();
			$v = new Vector3($x, $y, $z);
			$blocks[Clip::key($v)] = Block::get($reader->readByte(false), $reader->readByte(false));
		}
		return new Clip($blocks, null, $name);
	}
	public function offsetSet($name, $value){
		if(!($value instanceof Clip)){
			throw new \InvalidArgumentException("Trying to set value of a clipboard data provider to a non-clip");
		}
		$this->caches[$name] = $value;
		$writer = new StringWriter;
		$this->emit($writer, $value);
		$writer->save($this->getPath($name));
	}
	public static function emit(StringWriter $writer, Clip $clip){
		$writer->appendBString($clip->getName());
		$writer->appendLong(count($clip->getBlocks()));
		foreach($clip->getBlocks() as $key => $block){
			$v = Clip::unkey($key);
			$writer->appendInt($v->x);
			$writer->appendShort($v->y);
			$writer->appendInt($v->z);
			$writer->appendByte($block->getID());
			$writer->appendByte($block->getDamage());
		}
	}
	public function offsetUnset($name){
		@unlink($this->getPath($name));
	}
	public function getName(){
		return "Binary Clipboard Provider";
	}
	public function isAvailable(){
		return true;
	}
	public function close(){

	}
	public function collectGarbage(){
		$caches = $this->caches;
		foreach($caches as $name => $cache){
			if(microtime(true) - $cache->getCreationTime() >= $this->getMain()->getConfig()->get("data providers")["cache time"]){
				$this[$name] = $cache;
				unset($this->caches[$name]);
			}
		}
	}
}
