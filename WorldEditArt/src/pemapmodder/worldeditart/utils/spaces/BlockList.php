<?php

namespace pemapmodder\worldeditart\utils\spaces;

use pocketmine\block\Block;

class BlockList{
	protected $blocks = [];
	protected $safeMode;

	public function __construct($pattern, $safeMode = true){
		$this->safeMode = $safeMode;
		if($safeMode){
			$tokens = explode(",", $pattern);
			$hasPctg = [];
			$noPctg = [];
			foreach($tokens as $token){
				$ts = explode(":", $token);
				$damage = 0;
				if(isset($ts[1]) and is_numeric($ts[1])){
					$damage = intval($ts[1]);
				}
				$block = $ts[0];
				unset($ts); // 過河拆橋 :P
				$ts = explode("%", $block);
				unset($block); // another :D
				if(isset($ts[1])){
					$percent = array_shift($ts); // don't write $args again; I got too used to it.
				}
				$block = self::parseBlock($ts[0]);
				if($block === null){
					throw new BlockPatternParseException($pattern, "Block '$ts[0]' cannot be resolved.");
				}
				$blockObj = Block::get($block, $damage);
				unset($block); // again!
				if(isset($percent)){
					$hasPctg[] = [$percent, $blockObj];
				}else{
					$noPctg[] = $blockObj;
				}
			}
			$percent = 0;
			$map = [];
			foreach($hasPctg as $arr){
				$percent += $arr[0];
				$key = self::keyBlock($arr[1]);
				if(!isset($map[$key])){
					$map[$key] = 0;
				}
				$map[$key] += $arr[0];
			}
			if($percent > 100){
				throw new BlockPatternParseException($pattern, "Total percentage is larger than 100%");
			}
			$percent = 100 - $percent;
			if(count($noPctg) > 0){
				$each = $percent / count($noPctg);
				foreach($noPctg as $block){
					$key = self::keyBlock($block);
					if(!isset($map[$key])){
						$map[$key] = 0;
					}
					$map[$key] += $each;
				}
			}
			$this->blocks = $map;
		}else{
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
				}else{
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
			}elseif($left !== 0){
				throw new BlockPatternParseException($pattern, "Sum is not 100%");
			}
		}
	}

	/**
	 * @param $blockString
	 *
	 * @return int|null
	 */
	public static function parseBlock($blockString){
		if(strtolower(@strstr($blockString, ":", true)) === "minecraft"){
			$blockString = substr($blockString, strlen("minecraft:"));
		}
		if(is_numeric($blockString)){
			return (int) $blockString;
		}
		if(defined($const = "pocketmine\\block\\Block::" . strtoupper($blockString))){
			return constant($const);
		}
		try{
			if(class_exists($path = "pocketmine\\block\\$blockString")){
				$class = new \ReflectionClass($path);
				if(!$class->isAbstract()){
					return $class->newInstance()->getID();
				}
			}
		}catch(\Exception $e){
		}
		return null;
	}

	/**
	 * @param $string
	 *
	 * @return null|Block
	 */
	public static function getBlockFronString($string){
		$tokens = explode(":", $string);
		$block = self::parseBlock($tokens[0]);
		if($block === null){
			return null;
		}
		$meta = isset($tokens[1]) ? intval($tokens[1]) : 0;
		return Block::get($block, $meta);
	}

	/**
	 * @param $string
	 *
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

	public static function keyBlock(Block $block){
		return "{$block->getID()}:{$block->getDamage()}";
	}

	public static function unkeyBlock($key){
		$tokens = explode(":", $key);
		return Block::get($tokens[0], $tokens[1]);
	}

	public function getRandom(){
		if($this->safeMode){
			$rand = mt_rand();
			$float = $rand * 100 / mt_getrandmax();
			$cur = 0;
			foreach($this->blocks as $key => $percent){
				if($cur <= $float and $float < $percent){
					return self::unkeyBlock($key);
				}
			}
			return self::unkeyBlock(array_slice(array_keys($this->blocks), -1)[0]); // redundant return operation, but just in case
		}else{
			$rand = mt_rand();
			$value = 0;
			foreach($this->blocks as $arr){
				$value += $arr[1] / 100 * mt_getrandmax();
				if($value >= $rand){
					return $arr[0];
				}
			}
			trigger_error("BlockList::getRandom() got no blocks returned", E_USER_WARNING);
			return array_rand($this->blocks)[0];
		}
	}
}
