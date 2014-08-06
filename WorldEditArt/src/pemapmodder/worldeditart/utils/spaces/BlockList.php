<?php

namespace pemapmodder\worldeditart\utils\spaces;

use pocketmine\block\Block;

class BlockList{
	protected $blocks = [];
	public function __construct($pattern){
		$tokens = explode(",", $pattern);
		$withPercentage = [];
		$withoutPercentage = [];
		foreach($tokens as $token){
			if(preg_match('#^(\d+(\.\d+)?)%(minecraft:)?(\w+)(:(\d+))?$#', $token, $matches)){
				$match = $matches[0];
				$percentage = floatval($match[1]);
				$name = $match[4];
				$damage = 0;
				if(isset($match[6]) and $match[6]){
					$damage = intval($match[6]);
				}
				$block = Block::get(self::parseBlock($name), $damage);
				if(!($block instanceof Block)){
					throw new BlockPatternParseException($pattern, "Block \"$block\"not found");
				}
				$withPercentage[] = [$block, $percentage];
			}
			else{
				$ts = explode(":", $token);
				$damage = 0;
				if(isset($ts[1])){
					$damage = intval($ts[1]);
				}
				$block = Block::get($ts[0], $damage);
				$withoutPercentage[] = $block;
			}
		}
		$left = 100;
		foreach($withPercentage as $arr){
			$left -= $arr[1];
		}
		if($left < 0){
			throw new BlockPatternParseException($pattern, "Total percentage exceeds 100%");
		}
		$this->blocks = $withPercentage;
		if(count($withoutPercentage) > 0){
			$each = $left / count($withoutPercentage);
			foreach($withoutPercentage as $block){
				$this->blocks[] = [$block, $each];
			}
		}
		elseif($left !== 0){
			throw new BlockPatternParseException($pattern, "Sum is not 100%");
		}
	}
	/**
	 * @param $blockString
	 * @return int|null
	 */
	public static function parseBlock($blockString){
		if(is_numeric($blockString)){
			return (int) $blockString;
		}
		if(defined($const = "pocketmine\\block\\Block::".strtoupper($blockString))){
			return constant($const);
		}
		if(class_exists($path = "pocketmine\\block\\$blockString")){
			$class = new \ReflectionClass($path);
			if(!$class->isAbstract()){
				return $class->newInstance()->getID();
			}
		}
		return null;
	}
	/**
	 * @param $string
	 * @return null|Block
	 */
	public static function getBlockFronString($string){
		$tokens = explode(":", $string);
		$block = self::parseBlock($tokens[0]);
		if($block === null){
			return null;
		}
		$meta = isset($tokens[1]) ? intval($tokens[1]):0;
		return Block::get($block, $meta);
	}
	/**
	 * @param $string
	 * @return Block[]|null
	 */
	public static function getBlockArrayFromString($string){
		$out = [];
		foreach(explode(",", $string) as $name){
			$tokens = explode(":", $name);
			if(isset($tokens[1]) and $tokens[1] === "*"){
				$id = self::parseBlock($tokens[0]);
				if($id === null){
					return null;
				}
				for($i = 0; $i < 0x10; $i++){
					$out[] = Block::get($id, $i);
				}
				continue;
			}
			$b = self::getBlockFronString($name);
			if($b === null){
				return null;
			}
			$out[] = $b;
		}
		return $out;
	}
	public function getRandom(){
		$rand = mt_rand();
		$value = 0;
		foreach($this->blocks as $arr){
			$value += $arr[1] / 100 * mt_getrandmax();
			if($value >= $rand){
				return $arr[0];
			}
		}
		trigger_error("BlockList::getRandom() got no blocks returned", E_USER_ERROR);
		return array_rand($this->blocks)[0];
	}
}
