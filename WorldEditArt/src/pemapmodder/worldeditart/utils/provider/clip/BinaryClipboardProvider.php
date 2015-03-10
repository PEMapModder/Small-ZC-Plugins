<?php

namespace pemapmodder\worldeditart\utils\provider\clip;

use pemapmodder\worldeditart\WorldEditArt;
use pemapmodder\worldeditart\utils\clip\Clip;
use pemapmodder\worldeditart\utils\StringReader;
use pemapmodder\worldeditart\utils\StringWriter;
use pocketmine\block\Block;
use pocketmine\math\Vector3;

class BinaryClipboardProvider extends CachedClipboardProvider{
	public function __construct(WorldEditArt $main, $args){
		parent::__construct($main);
		$this->path = $main->getDataFolder().$args["path"];
	}
	public function getPath($name){
		return str_replace("<name>", strtolower($name), $this->path);
	}
	public function getClip($name){
		$path = $this->getPath($name);
		if(!is_file($path)){
			return null;
		}
		try{
			$reader = StringReader::fromFile($path);
		}
		catch(\Exception $e){
			$this->getMain()->getLogger()->error("Error parsing global clip $name: ".$e->getMessage());
			return null;
		}
		$clip = self::parse($reader);
		return $clip;
	}
	public static function parse(StringReader $reader){
		$name = $reader->readBString();
		$length = $reader->readLong(false);
		$blocks = [];
		for($i = 0; $i < $length; $i++){
			if($reader->feof()){
				throw new \UnderflowException("Unexpected end of file");
			}
			$x = $reader->readInt();
			$y = $reader->readShort();
			$z = $reader->readInt();
			$v = new Vector3($x, $y, $z);
			$blocks[Clip::key($v)] = Block::get($reader->readByte(false), $reader->readByte(false));
		}
		return new Clip($blocks, null, $name);
	}
	public function setClip($name, Clip $clip){
		$writer = new StringWriter;
		$this->emit($writer, $clip);
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
	public function deleteClip($name){
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
}
