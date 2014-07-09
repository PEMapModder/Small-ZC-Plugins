<?php

namespace pemapmodder\worldeditart;

use pemapmodder\worldeditart\utils\macro\RecordingMacro;
use pemapmodder\worldeditart\utils\spaces\Space;
use pemapmodder\worldeditart\utils\subcommand\Anchor;
use pemapmodder\worldeditart\utils\subcommand\Macro;
use pemapmodder\worldeditart\utils\subcommand\Sel;
use pemapmodder\worldeditart\utils\subcommand\Set;
use pemapmodder\worldeditart\utils\subcommand\SubcommandMap;
use pemapmodder\worldeditart\utils\subcommand\Test;
use pemapmodder\worldeditart\utils\subcommand\Wand;
use pocketmine\block\Block;
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\block\BlockPlaceEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\item\Item;
use pocketmine\level\Position;
use pocketmine\math\Vector3;
use pocketmine\Player;
use pocketmine\plugin\PluginBase;
use pocketmine\scheduler\PluginTask;
use pocketmine\utils\Binary;

class Main extends PluginBase implements Listener{
	/** @var PluginTask[] */
	private $mustEnds;
	/** @var array[] */
	private $clips = [];
	/** @var Position[] $selectedPoints */
	private $selectedPoints = [];
	/** @var utils\spaces\Space[] */
	private $selections = [];
	/** @var Position[] */
	private $anchors = [];
	private $macros = [];
	private $globalClipPath;
	public function onLoad(){
		$this->saveDefaultConfig();
		$maxHeight = $this->getConfig()->get("maximum world height");
		if(!defined($path = "pemapmodder\\worldeditart\\MAX_WORLD_HEIGHT")){
			define($path, $maxHeight);
		}
	}
	public function onEnable(){
		@mkdir($this->getDataFolder());
		@mkdir($this->getDataFolder()."players/");
		$this->saveDefaultConfig();
		$this->getServer()->getPluginManager()->registerEvents($this, $this);
		$this->registerCommands();
		@mkdir($this->globalClipPath = $this->getDataFolder()."clips/");
	}
	public function onDisable(){
		/*foreach($this->mustEnds as $id => $task){
			$this->getServer()->getScheduler()->cancelTask($id);
			$task->onRun(-1);
		}*/
	}
	private function registerCommands(){
		$wea = new SubcommandMap("worldeditart", $this, "WorldEditArt main command", "wea.cmd", ["wea", "we", "w"]); // I expect them to use fallback prefix if they use /w
		$wea->registerAll([
			new Anchor($this),
			new Macro($this),
			new Sel($this),
			new Set($this),
			new Test($this),
			new Wand($this),
		]);
		$this->getServer()->getCommandMap()->register("wea", $wea);
	}
	public function onJoin(PlayerJoinEvent $event){

	}
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
	}
	public function getClip(Player $player){
		return isset($this->clips[$player->getID()])?$this->clips[$player->getID()]:false;
	}
	/**
	 * @param PlayerInteractEvent $event
	 * @priority MONITOR
	 * @ignoreCancelled true
	 */
	public function onInteract(PlayerInteractEvent $event){
		$p = $event->getPlayer();
		if($this->isWand($p, $event->getItem()) and $p->hasPermission("wea.sel.pt.wand")){
			$this->setSelectedPoint($p, $event->getBlock());
		}
	}
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
	/**
	 * @param Player $player
	 * @return Position|bool
	 */
	public function getSelectedPoint(Player $player){
		if(isset($this->selectedPoints[$player->getID()])){
			return $this->selectedPoints[$player->getID()];
		}
		return false;
	}
	public function setSelectedPoint(Player $player, Position $pos){
		$this->selectedPoints[$player->getID()] = clone $pos;
	}
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
	public function getPlayerFile(Player $player){
		return $this->getDataFolder()."players/".strtolower($player->getName());
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
	public function scheduleMustEndEvent(PluginTask $task, $delay){
		$id = $this->getServer()->getScheduler()->scheduleDelayedTask($task, $delay)->getTaskId();
		$this->mustEnds[$id] = $task;
	}
	public function getAnchor(Player $player){
		return isset($this->anchors[$player->getID()]) ? $this->anchors[$player->getID()]:false;
	}
	public function setAnchor(Player $player, Position $anchor){
		$this->anchors[$player->getID()] = clone $anchor;
	}
	public function isGlobalClipCreated($name){
		return is_file($this->globalClipPath.$name.".clp");
	}
	public function getGlobalClip($name){
		if(!$this->isGlobalClipCreated($name)){
			return false;
		}
		$res = gzopen($this->globalClipPath.$name.".clp", "rb");
		if(!is_resource($res)){
			return false;
		}
		$author = gzread($res, ord(gzread($res, 1)));
		$cnt = Binary::readLong(gzread($res, 8), false);
		/** @var Block[] $blocks */
		$blocks = [];
		for($i = 0; $i < $cnt and !gzeof($res); $i++){
			$x = Binary::readLong(gzread($res, 8));
			$y = Binary::readShort(gzread($res, 2));
			$z = Binary::readLong(gzread($res, 8));
			$id = ord(gzread($res, 1));
			$damage = ord(gzread($res, 1));
			$blocks[] = Block::get($id, $damage, new Position($x, $y, $z, $this->getServer()->getDefaultLevel())); // any placeholder level will do.
		}
		gzclose($res);
		if(count($blocks) !== $cnt){
			trigger_error("Global clip $name was corrupted", E_USER_WARNING);
			return false;
		}
		return [
			"author" => $author,
			"blocks" => $blocks
		];
	}
	public function saveGlobalClip($name, array $data){
		$path = $this->globalClipPath.$name.".clp";
		@unlink($path);
		$res = gzopen($this->globalClipPath.$name.".clp", "wb");
		if(!is_resource($res)){
			return "Unable to open stream for \"$name.clp\". Check if \"$name\" is a valid filename.";
		}
		gzwrite($res, chr(strlen($data["author"])));
		gzwrite($res, $data["author"]);
		gzwrite($res, Binary::writeLong(count($data["blocks"])));
		/** @var Block $block */
		foreach($data["blocks"] as $block){
			gzwrite($res, Binary::writeLong($block->getX()));
			gzwrite($res, Binary::writeShort($block->getY()));
			gzwrite($res, Binary::writeLong($block->getZ()));
			gzwrite($res, chr($block->getID()));
			gzwrite($res, chr($block->getDamage()));
		}
		gzclose($res);
		$this->getLogger()->info("New clip \"$name\" has been created on the global clipboard as $name.clp.");
		return true;
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
}
