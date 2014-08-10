<?php

namespace pemapmodder\worldeditart;

use pemapmodder\worldeditart\utils\clip\Clip;
use pemapmodder\worldeditart\utils\macro\Macro;
use pemapmodder\worldeditart\utils\provider\clip\BinaryClipboardProvider;
use pemapmodder\worldeditart\utils\provider\clip\DummyClipboardProvider;
use pemapmodder\worldeditart\utils\provider\clip\MysqliClipboardProvider;
use pemapmodder\worldeditart\utils\provider\macro\DummyMacroDataProvider;
use pemapmodder\worldeditart\utils\provider\macro\LocalNBTMacroDataProvider;
use pemapmodder\worldeditart\utils\provider\macro\MysqliMacroDataProvider;
use pemapmodder\worldeditart\utils\provider\player\DummyPlayerDataProvider;
use pemapmodder\worldeditart\utils\provider\player\JSONFilePlayerDataProvider;
use pemapmodder\worldeditart\utils\provider\player\MysqliPlayerDataProvider;
use pemapmodder\worldeditart\utils\provider\player\PlayerData;
use pemapmodder\worldeditart\utils\provider\player\SQLite3PlayerDataProvider;
use pemapmodder\worldeditart\utils\provider\player\YAMLFilePlayerDataProvider;
use pemapmodder\worldeditart\utils\spaces\CylinderSpace;
use pemapmodder\worldeditart\utils\spaces\Space;
use pemapmodder\worldeditart\utils\subcommand\Copy;
use pemapmodder\worldeditart\utils\subcommand\Cuboid;
use pemapmodder\worldeditart\utils\subcommand\Cut;
use pemapmodder\worldeditart\utils\subcommand\Cylinder;
use pemapmodder\worldeditart\utils\subcommand\Macro as MacroSubcommand;
use pemapmodder\worldeditart\utils\subcommand\Paste;
use pemapmodder\worldeditart\utils\subcommand\PosSubcommand;
use pemapmodder\worldeditart\utils\subcommand\Replace;
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
use pocketmine\event\server\DataPacketReceiveEvent;
use pocketmine\item\Item;
use pocketmine\level\Position;
use pocketmine\math\Vector3;
use pocketmine\network\protocol\UseItemPacket;
use pocketmine\Player;
use pocketmine\plugin\PluginBase;

class Main extends PluginBase implements Listener{
////////////
// FIELDS //
////////////

// SESSIONING FIELDS
	/** @var array[] */
	private $clips = [];
	/** @var Position[] */
	private $selectedPoints = [];
	/** @var utils\spaces\Space[] */
	private $selections = [];
	/** @var Position[] */
	private $anchors = [];
	/** @var Macro[] */
	private $macros = [];
	/** @var array[] */
	private $tempPos = [];
// DATA PROVIDERS
	/** @var utils\provider\clip\ClipboardProvider */
	private $clipboardProvider;
	/** @var utils\provider\macro\MacroDataProvider */
	private $macroDataProvider;
	/** @var utils\provider\player\PlayerDataProvider */
	private $playerDataProvider;
	/** @var null|\mysqli */
	private $commonMysqli = null;

// INITIALIZERS
	public function onPreEnable(){
		@mkdir($this->getDataFolder());
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
		$providers = $this->getConfig()->get("data providers");

		$players = $providers["player"];
		$type = $players["type"];
		switch(strtolower($type)){
			case "sqlite3":
				$this->playerDataProvider = new SQLite3PlayerDataProvider($this, $players["sqlite3"]["path"]);
				break;
			case "json":
				$this->playerDataProvider = new JSONFilePlayerDataProvider($this, $players["json"]["path"]);
				break;
			case "yaml":
				$this->playerDataProvider = new YAMLFilePlayerDataProvider($this, $players["yaml"]["path"]);
				break;
			case "mysqli":
				if($players["mysqli"]["use common"]){
					$db = $this->getCommonMysqli();
				}
				else{
					$args = $players["mysqli"];
					$db = new \mysqli($args["host"], $args["username"], $args["password"], $args["database"], $args["port"]);
					if($db->connect_error){
						$this->getLogger()->critical("Cannot connect to MySQLi remote database. The database will be assumed blank and read-only.");
						$this->playerDataProvider = new DummyPlayerDataProvider($this);
						break;
					}
				}
				$this->playerDataProvider = new MysqliPlayerDataProvider($this, $db);
				break;
			default:
				$this->getLogger()->critical("Unknown player data provider type $type. The database will be assumed blank and read-only.");
				$this->playerDataProvider = new DummyPlayerDataProvider($this);
				break;
		}

		$clipboard = $providers["clipboard"];
		switch(strtolower($clipboard["type"])){
			case "clp":
				$this->clipboardProvider = new BinaryClipboardProvider($this, $clipboard["clp"]);
				break;
			case "mysqli":
				$args = $clipboard["mysqli"];
				if($args["use common"]){
					$db = $this->getCommonMysqli();
				}
				else{
					$db = new \mysqli($args["host"], $args["username"], $args["password"], $args["database"], $args["port"]);
					if($db->connect_error){
						$this->getLogger()->critical("Cannot connect to MySQLi remote database. The database will be assumed blank and read-only.");
						$this->clipboardProvider = new DummyClipboardProvider($this);
						break;
					}
				}
				$this->clipboardProvider = new MysqliClipboardProvider($this, $db);
				break;
			default:
				$this->getLogger()->critical("Unknown clipboard provider type ".$clipboard["type"].". A temporary-memory clipboard provider will be used.");
				$this->clipboardProvider = new DummyClipboardProvider($this);
				break;
		}

		$macros = $providers["macro"];
		$type = $macros["type"];
		switch(strtolower($type)){
			case "mcr":
				$this->macroDataProvider = new LocalNBTMacroDataProvider($this, $macros["mcr"]["path"]);
				break;
			case "mysqli":
				$mysqli = $macros["mysqli"];
				if($mysqli["use common"]){
					$db = $this->getCommonMysqli();
					if($db === null){
						$this->getLogger()->critical("Unable to connect to the MySQLi database of macros. A RAM database will be used.");
						$this->macroDataProvider = new DummyMacroDataProvider($this);
						break;
					}
				}
				else{
					$db = new \mysqli($mysqli["host"], $mysqli["username"], $mysqli["password"], $mysqli["database"], $mysqli["port"]);
					if($db->connect_error){
						$this->getLogger()->critical("Unable to connect to the MySQLi database of macros. A RAM database will be used.");
					}
				}
				$this->macroDataProvider = new MysqliMacroDataProvider($this, $db);
		}
	}
	private function registerCommands(){
		$wea = new SubcommandMap("worldeditart", $this, "WorldEditArt main command", "wea.cmd", ["wea", "we", "w", "/"]); // I expect them to use fallback prefix if they use /w
		$wea->registerAll([
			new Copy($this),
			new Cuboid($this),
			new Cut($this),
			new Cylinder($this),
			new MacroSubcommand($this),
			new Paste($this),
			new PosSubcommand($this, false),
			new PosSubcommand($this, true),
			new Replace($this),
			new Set($this),
			new Test($this),
			new Wand($this)
		]);
		$this->getServer()->getCommandMap()->register("wea", $wea);
	}
	public function onDisable(){
		$this->macroDataProvider->close();
		$this->playerDataProvider->close();
		$this->clipboardProvider->close();
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
		if(($macro = $this->getRecordingMacro($player)) instanceof Macro){
			$macro->addBlock($isBreak ? new Air:$block);
		}
	}
	public function onPacketReceived(DataPacketReceiveEvent $event){
		$pk = $event->getPacket();
		$player = $event->getPlayer();
		if($pk instanceof UseItemPacket and $pk->face === 0xff){
			/** @var PlayerData $data */
			$data = $this->getPlayerDataProvider()[$player->getName()];
			if($data->checkJump($player->getInventory()->getItemInHand())){
				$target = self::getCrosshairTarget($player, 0.5, PHP_INT_MAX); // config.yml
				while(true){
					$target = $target->add(0, 1);
					$block = $player->getLevel()->getBlock($target);
					if(!($block instanceof Block)){
						break;
					}
					$nonSolids = [Block::AIR, Block::WATER, Block::STILL_WATER, Block::LAVA, Block::STILL_LAVA];
					if(in_array($block->getID(), $nonSolids)){
						break;
					}
				}
				$player->teleport($target);
			}
		}
	}

/////////////////
// SESSIONINGS //
/////////////////

// CLIPBOARD
	/**
	 * @param Player $player
	 * @param string $name
	 * @return Clip|bool
	 */
	public function getClip(Player $player, $name = "default"){
		return isset($this->clips[$player->getID()]) and isset($this->clips[$player->getID()][$name]) ?
			$this->clips[$player->getID()][$name]:false;
	}
	/**
	 * @param Player $player
	 * @param Clip $clip
	 * @param string|bool $name
	 */
	public function setClip(Player $player, Clip $clip, $name = false){
		if($name === false){
			$name = $clip->getName();
		}
		if(!isset($this->clips[$player->getID()])){
			$this->clips[$player->getID()] = [];
		}
		$this->clips[$player->getID()][$name] = $clip;
	}
// MACROS
	/**
	 * @param Player $player
	 * @return Macro|bool
	 */
	public function getRecordingMacro(Player $player){
		return isset($this->macros[$player->getID()]) ? $this->macros[$player->getID()]:false;
	}
	/**
	 * @param Player $player
	 * @param Macro $macro
	 */
	public function setRecordingMacro(Player $player, Macro $macro){
		$this->macros[$player->getID()] = $macro;
	}
	/**
	 * @param Player $player
	 */
	public function unsetRecordingMacro(Player $player){
		unset($this->macros[$player->getID()]);
	}
// WANDS
	/**
	 * @param Player $player
	 * @param $id
	 * @param bool $damage
	 */
	public function setWand(Player $player, $id, $damage = PlayerData::ALLOW_ANY){
		/** @var PlayerData $data */
		$data = $this->playerDataProvider[$player->getName()];
		$data->setWandID($id);
		$data->setWandDamage($damage);
	}
	/**
	 * @param Player $player
	 * @param Item $item
	 * @return bool
	 */
	public function isWand(Player $player, Item $item){
		/** @var PlayerData $data */
		$data = $this->playerDataProvider[$player->getName()];
		return $data->checkWand($item);
	}
// SELECTIONS
	/**
	 * @param Player $player
	 * @param Space $space
	 */
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
	/**
	 * @param Player $player
	 * @return bool
	 */
	public function unsetSelection(Player $player){
		if(isset($this->selections[$player->getID()])){
			unset($this->selections[$player->getID()]);
			return true;
		}
		return false;
	}
	// Cuboid Selections
	/**
	 * @param Player $player
	 * @return array|bool
	 */
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
	/**
	 * @param Player $player
	 * @return bool|Position
	 */
	public function getAnchor(Player $player){
		return isset($this->anchors[$player->getID()]) ? $this->anchors[$player->getID()]:false;
	}
	/**
	 * @param Player $player
	 * @param Position $anchor
	 */
	public function setAnchor(Player $player, Position $anchor){
		$this->anchors[$player->getID()] = clone $anchor;
	}

///////////
// UTILS //
///////////

// INSTANCE GETTERS
	public function getCommonMysqli(){
		if(!($this->commonMysqli instanceof \mysqli)){
			$data = $this->getConfig()->get("data providers")["common mysqli database"];
			$this->commonMysqli = new \mysqli($data["host"], $data["username"], $data["password"], $data["database"]);
			if($this->commonMysqli->connect_error){
				$this->getLogger()->critical("Error trying to connect to common MySQLi database: ".$this->commonMysqli->connect_error);
				return null;
			}
		}
		return $this->commonMysqli;
	}
	/**
	 * @param $name
	 * @return PlayerData
	 */
	public function getPlayerData($name){
		return $this->playerDataProvider[$name];
	}

// STATIC UTILS
	/**
	 * @param Position $pos
	 * @return string
	 */
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
		return $out;
	}
	private static function rotateBlockByOne(Block $block){
		return Block::get($block->getID(), $block->getDamage(), new Position($block->getZ(), $block->getY(), -$block->getX(), $block->getLevel()));
	}
	public static function getCrosshairTarget(Entity $entity, $accuracy = 0.5, $max = PHP_INT_MAX){
		$found = null;
		$direction = $entity->getDirectionVector()->multiply($accuracy);
		/** @var Vector3 $last */
		for($last = null, $pos = $entity->add($direction), $i = 1; $i * $accuracy <= $max; $last = $pos->floor(), $pos = $entity->add($direction->multiply(++$i))){
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
	/**
	 * @return \pemapmodder\worldeditart\utils\provider\player\PlayerDataProvider
	 */
	public function getPlayerDataProvider(){
		return $this->playerDataProvider;
	}
	/**
	 * @return \pemapmodder\worldeditart\utils\provider\macro\MacroDataProvider
	 */
	public function getMacroDataProvider(){
		return $this->macroDataProvider;
	}
	/**
	 * @return \pemapmodder\worldeditart\utils\provider\clip\ClipboardProvider
	 */
	public function getClipboardProvider(){
		return $this->clipboardProvider;
	}
}
