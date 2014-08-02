<?php

namespace pemapmodder\worldeditart;

use pemapmodder\worldeditart\utils\macro\RecordingMacro;
use pemapmodder\worldeditart\utils\spaces\CylinderSpace;
use pemapmodder\worldeditart\utils\spaces\Space;
use pemapmodder\worldeditart\utils\subcommand\Cuboid;
use pemapmodder\worldeditart\utils\subcommand\Cylinder;
use pemapmodder\worldeditart\utils\subcommand\PosSubcommand;
use pemapmodder\worldeditart\utils\subcommand\Set;
use pemapmodder\worldeditart\utils\subcommand\SubcommandMap;
use pemapmodder\worldeditart\utils\subcommand\Test;
use pemapmodder\worldeditart\utils\subcommand\Wand;
use pocketmine\block\Air;
use pocketmine\block\Block;
use pocketmine\entity\Entity;
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\block\BlockPlaceEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\item\Item;
use pocketmine\level\Position;
use pocketmine\math\Vector3;
use pocketmine\Player;
use pocketmine\plugin\PluginBase;

class Main extends PluginBase implements Listener{

///////////////////////
// SESSIONING FIELDS //
///////////////////////
	/** @var array[] */
	private $clips = [];
	/** @var Position[] */
	private $selectedPoints = [];
	/** @var utils\spaces\Space[] */
	private $selections = [];
	/** @var Position[] */
	private $anchors = [];
	/** @var RecordingMacro[] */
	private $macros = [];
	/** @var array[] */
	private $tempPos = [];
	private $globalClipPath;

// INITIALIZERS
	public function onPreEnable(){
		@mkdir($this->getDataFolder());
		@mkdir($this->getDataFolder()."players/");
		$this->saveDefaultConfig();
		$maxHeight = $this->getConfig()->get("maximum world height");
		if(!defined($path = "pemapmodder\\worldeditart\\MAX_WORLD_HEIGHT")){
			define($path, $maxHeight);
		}
	}
	public function onEnable(){
		$this->onPreEnable();
		$this->getServer()->getPluginManager()->registerEvents($this, $this);
		$this->registerCommands();
		@mkdir($this->globalClipPath = $this->getDataFolder()."clips/");
	}
	private function registerCommands(){
		$wea = new SubcommandMap("worldeditart", $this, "WorldEditArt main command", "wea.cmd", ["wea", "we", "w", "/"]); // I expect them to use fallback prefix if they use /w
		$wea->registerAll([
			new Cuboid($this),
			new Cylinder($this),
			new PosSubcommand($this, false),
			new PosSubcommand($this, true),
			new Set($this),
			new Test($this),
			new Wand($this)
		]);
		$this->getServer()->getCommandMap()->register("wea", $wea);
	}

////////////////////
// EVENT HANDLERS //
////////////////////
	public function onQuit(PlayerQuitEvent $event){
		$i = $event->getPlayer()->getID();
		if(isset($this->selectedPoints[$i])){
			unset($this->selectedPoints[$i]);
		}
		if(isset($this->selections[$i])){
			unset($this->selections[$i]);
		}
		if(isset($this->anchors[$i])){
			unset($this->anchors[$i]);
		}
		if(isset($this->macros[$i])){
			unset($this->macros[$i]);
		}
	}
	/**
	 * Recognizes a wand operation if item in hand matches wand
	 *
	 * @param PlayerInteractEvent $event
	 * @priority HIGH
	 */
	public function onInteract(PlayerInteractEvent $event){
		$p = $event->getPlayer();
		if($this->isWand($p, $event->getItem())){
			$this->setAnchor($p, $event->getBlock());
			$event->setCancelled();
		}
	}
	/**
	 * @param BlockPlaceEvent $event
	 * @priority MONITOR
	 * @ignoreCancelled true
	 */
	public function onBlockPlace(BlockPlaceEvent $event){
		$this->onBlockTouched($event->getPlayer(), $event->getBlock(), false);
	}
	/**
	 * @param BlockBreakEvent $event
	 * @priority MONITOR
	 * @ignoreCancelled true
	 */
	public function onBlockBreak(BlockBreakEvent $event){
		$this->onBlockTouched($event->getPlayer(), $event->getBlock(), true);
	}
	/**
	 * @param Player $player
	 * @param Block $block
	 * @param bool $isBreak
	 */
	public function onBlockTouched(Player $player, Block $block, $isBreak){
		if(($macro = $this->getRecordingMacro($player)) instanceof RecordingMacro){
			$macro->addLog($block, $block, $isBreak);
		}
	}

/////////////////
// SESSIONINGS //
/////////////////

// CLIPBOARD
	public function getClip(Player $player){
		return isset($this->clips[$player->getID()])?$this->clips[$player->getID()]:false;
	}
	public function setClip(Player $player, array $data){
		$this->clips[$player->getID()] = $data;
	}
	// MACROS
	/**
	 * @param Player $player
	 * @return RecordingMacro|bool
	 */
	public function getRecordingMacro(Player $player){
		return isset($this->macros[$player->getID()]) ? $this->macros[$player->getID()]:false;
	}
	public function setRecordingMacro(Player $player, RecordingMacro $macro){
		$this->macros[$player->getID()] = $macro;
	}
	// WANDS
	public function getPlayerWand(Player $player, &$isDamageLimited){
		$id = false;
		$damage = false;
		if(is_file($path = $this->getPlayerFile($player))){
			$data = yaml_parse_file($path);
			$id = $data["wand-id"];
			$damage = $data["wand-damage"];
		}
		if($id === false){
			$id = $this->getConfig()->get("wand-id");
		}
		if($damage === false){
			$damage = $this->getConfig()->get("wand-damage");
		}
		$isDamageLimited = is_int($damage);
		if($damage === true){
			$damage = 0;
		}
		return Item::get($id, $damage);
	}
	public function setWand(Player $player, $id, $damage = true){
		if(!is_file($path = $this->getPlayerFile($player))){
			stream_copy_to_stream($this->getResource("player.yml"), fopen($path, "wb"));
		}
		$yaml = yaml_parse_file($path);
		$yaml["wand-id"] = $id;
		$yaml["wand-damage"] = $damage;
		yaml_emit_file($path, $yaml, YAML_UTF8_ENCODING);
	}
	public function isWand(Player $player, Item $item){
		$path = $this->getPlayerFile($player);
		$id = false;
		$damage = false;
		if(is_file($path)){
			$data = yaml_parse_file($path);
			$id = $data["wand-id"];
			$damage = $data["wand-damage"];
		}
		if($id === false){
			$id = $this->getConfig()->get("wand-id");
		}
		if($damage === false){
			$damage = $this->getConfig()->get("wand-damage");
		}
		if($id !== $item->getID()){
			return false;
		}
		if($damage === true or $damage === $item->getDamage()){
			return true;
		}
		return false;
	}
	// SELECTIONS
	public function setSelection(Player $player, Space $space){
		$this->selections[$player->getID()] = clone $space;
	}
	/**
	 * @param Player $player
	 * @return bool|Space
	 */
	public function getSelection(Player $player){
		return isset($this->selections[$player->getID()]) ? $this->selections[$player->getID()]:false;
	}
	public function unsetSelection(Player $player){
		if(isset($this->selections[$player->getID()])){
			unset($this->selections[$player->getID()]);
			return true;
		}
		return false;
	}
	// Cuboid Selections
	public function getTempPos(Player $player){
		return isset($this->tempPos[$player->getID()]) ? $this->tempPos[$player->getID()]:false;
	}
	/**
	 * @param Player $player
	 * @param Position $pos
	 * @param bool $isTwo
	 */
	public function setTempPos(Player $player, Position $pos, $isTwo){
		$this->tempPos[$player->getID()] = ["position" => clone $pos, "#" => $isTwo];
	}
	// ANCHORS
	public function getAnchor(Player $player){
		return isset($this->anchors[$player->getID()]) ? $this->anchors[$player->getID()]:false;
	}
	public function setAnchor(Player $player, Position $anchor){
		$this->anchors[$player->getID()] = clone $anchor;
	}

///////////
// UTILS //
///////////
	public function getPlayerFile(Player $player){
		return $this->getDataFolder()."players/".strtolower($player->getName());
	}
	public static function posToStr(Position $pos){
		return self::v3ToStr($pos)." in world \"{$pos->getLevel()->getName()}\"";
	}
	public static function v3ToStr(Vector3 $v3){
		return "({$v3->x}, {$v3->y}, {$v3->z})";
	}
	/**
	 * @param string $block
	 * @return bool|Block
	 */
	public static function parseBlock($block){
		$damage = 0;
		if(strpos($block, ":") !== false){
			$tokens = explode(":", $block);
			$damage = (int) $tokens[1];
			$block = $tokens[0];
		}
		$path = "pocketmine\\block\\$block";
		if(defined("pocketmine\\block\\Block::$block")){
			$id = constant("pocketmine\\block\\Block::$block");
		}
		elseif(class_exists($path) and is_subclass_of($path, "pocketmine\\block\\Block")){
			/** @var Block $instance */
			$instance = new $block;
			$id = $instance->getID();
		}
		elseif(is_numeric($block)){
			$id = (int) $block;
		}
		else{
			return false;
		}
		return Block::get($id, $damage);
	}
	/**
	 * @param Block[] $blocks
	 * @param int $from
	 * @param int $to
	 * @return Block[]
	 */
	public static function rotateBlocks(array $blocks, $from, $to){
		while($from > $to){
			$to += 4;
		}
		$diff = ($to - $from) % 4;
		while($diff > 0){
			$blocks = self::rotateBlocksByOne($blocks);
			$diff--;
		}
		return $blocks;
	}
	/**
	 * @param Block[] $blocks
	 * @return Block[]
	 */
	private static function rotateBlocksByOne(array $blocks){
		$out = [];
		foreach($blocks as $key => $block){
			$out[$key] = self::rotateBlockByOne($block);
		}
	}
	private static function rotateBlockByOne(Block $block){
		return Block::get($block->getID(), $block->getDamage(), new Position($block->getZ(), $block->getY(), -$block->getX(), $block->getLevel()));
	}
	public static function getCrosshairTarget(Entity $entity){
		$found = null;
		$direction = $entity->getDirectionVector()->multiply(0.5);
		/** @var Vector3 $last */
		for($last = null, $pos = $entity->add($direction), $i = 1; true; $last = $pos->floor(), $pos = $entity->add($direction->multiply(++$i))){
			if($last instanceof Vector3){
				if($last->x === $pos->getFloorX() and $last->y === $pos->getFloorY() and $last->z === $pos->getFloorZ()){
					continue;
				}
				if($pos->y < 0){
					break;
				}
				$maxY = 127;
				if(defined($path = "pemapmodder\\worldeditart\\MAX_WORLD_HEIGHT")){
					$maxY = constant($path);
				}
				if($pos->y > $maxY + 1){
					break;
				}
				$block = $entity->getLevel()->getBlock($pos);
				if(!($block instanceof Block)){
					break;
				}
				if($block instanceof Air){
					continue;
				}
				$found = $block;
				break;
			}
		}
		return $found;
	}
	/**
	 * @param $direction
	 * @return array
	 */
	public static function directionNumber2Array($direction){
		if($direction instanceof Entity){
			$direction = $direction->getDirection();
		}
		switch($direction){
			case 0:
				return [CylinderSpace::Y, CylinderSpace::MINUS];
			case 1:
				return [CylinderSpace::Y, CylinderSpace::PLUS];
			case 2:
				return [CylinderSpace::Z, CylinderSpace::MINUS];
			case 3:
				return [CylinderSpace::Z, CylinderSpace::PLUS];
			case 4:
				return [CylinderSpace::X, CylinderSpace::MINUS];
		}
		return [CylinderSpace::X, CylinderSpace::PLUS];
	}
	public static function rotateDirectionNumberClockwise($number, $quarters = 1){
		if($quarters > 1){
			for($i = 0; $i < $quarters; $i++){
				$number = self::rotateDirectionNumberClockwise($number);
			}
			return $number;
		}
		switch($number){
			case 2:
				return 4;
			case 3:
				return 5;
			case 4:
				return 3;
			case 5:
				return 2;
			default:
				return $number;
		}
	}
}
