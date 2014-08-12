<?php

namespace pemapmodder\worldeditart\utils\provider\macro;

use pemapmodder\worldeditart\Main;
use pemapmodder\worldeditart\utils\macro\Macro;
use pemapmodder\worldeditart\utils\macro\MacroOperation;
use pocketmine\block\Block;
use pocketmine\math\Vector3;
use pocketmine\nbt\NBT;
use pocketmine\nbt\tag;

class LocalNBTMacroDataProvider extends CachedMacroDataProvider{
	/** @var string */
	private $path;
	private $compression;
	public function __construct(Main $main, $path, $compression){
		parent::__construct($main);
		$this->path = $path;
		$this->compression = $compression;
	}
	public function isAvailable(){
		return true;
	}
	public function close(){

	}
	public function getName(){
		return "Local NBT Macro Data Provider";
	}
	public function readMacro($name){
		if(!is_file($path = $this->getFile($name))){
			return null;
		}
		$string = file_get_contents($path);
		$nbt = new NBT;
		$type = ord(substr($string, 0, 1));
		$string = substr($string, 1);
		if($type === 0){
			$nbt->read($string);
		}
		else{
			$nbt->readCompressed($string, $type);
		}
		$tag = $nbt->getData();
		$author = $tag["author"];
		$description = $tag["description"];
		/** @var tag\Enum $tags */
		$tags = $tag["ops"];
		$ops = [];
		/** @var tag\Compound $t */
		foreach($tags as $t){
			$type = $tag["type"];
			if($type === 1){
				$ops[] = new MacroOperation($t["delta"]);
			}
			else{
				$vectors = $t["vectors"];
				$ops[] = new MacroOperation(new Vector3($vectors[0], $vectors[1], $vectors[2]), Block::get($t["blockID"], $t["blockDamage"]));
			}
		}
		return new Macro(false, $ops, $author, $description);
	}
	public function saveMacro($name, Macro $macro){
		$tag = new tag\Compound;
		$tag["author"] = new tag\String("author", $macro->getAuthor());
		$tag["description"] = new tag\String("description", $macro->getDescription());
		$tag["ops"] = new tag\Enum("ops");
		foreach($macro->getOperations() as $i => $log){
			$tag["ops"][$i] = $log->toTag();
		}
		$nbt = new NBT;
		$nbt->setData($tag);
		$file = $this->getFile($name);
		$stream = @fopen($file, "wb");
		if(!is_resource($stream)){
			throw new \RuntimeException("Unable to open stream. Maybe the macro name is not a valid filename?");
		}
		$compression = $this->compression;
		if($compression === 0){
			$data = $nbt->write();
		}
		else{
			$data = $nbt->writeCompressed($compression);
		}
		fwrite($stream, chr($compression).$data);
		fclose($stream);
	}
	public function deleteMacro($name){
		unlink($this->getFile($name));
	}
	public function getFile($name){
		$file = $this->getMain()->getDataFolder().str_replace("<name>", $name, $this->path);
		@mkdir($name, 0777, true);
		return $file;
	}
}
