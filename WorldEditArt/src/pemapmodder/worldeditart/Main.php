<?php

namespace pemapmodder\worldeditart;

use pemapmodder\worldeditart\utils\spaces\Space;
use pemapmodder\worldeditart\utils\subcommand\Macro;
use pemapmodder\worldeditart\utils\subcommand\SubcommandMap;
use pemapmodder\worldeditart\utils\subcommand\Wand;
use pocketmine\block\Block;
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

class Main extends PluginBase implements Listener{
	const W_NOTHING = 0;
	/** @var PluginTask[] */
	private $mustEnds;
	/** @var int[] $wandSessions */
	private $wandSessions = [];
	/** @var Position[] $selectedPoints */
	private $selectedPoints = [];
	/** @var utils\spaces\Space[] */
	private $selections = [];
	public function onEnable(){
		@mkdir($this->getDataFolder());
		@mkdir($this->getDataFolder()."players/");
		$this->saveDefaultConfig();
		$this->getConfig(); // just to load it
		$this->getServer()->getPluginManager()->registerEvents($this, $this);
		$this->registerCommands();
	}
	public function onDisable(){
		foreach($this->mustEnds as $id => $task){
			$this->getServer()->getScheduler()->cancelTask($id);
			$task->onRun(-1);
		}
	}
	private function registerCommands(){
		$wea = new SubcommandMap("worldeditart", $this, "WorldEditArt main command", "wea.cmd", ["wea", "we", "w"]); // I expect them to use fallback prefix if they use /w
		$wea->registerAll([
			new Macro($this),
			new Wand($this),
		]);
		$this->getServer()->getCommandMap()->register("wea", $wea);
	}
	public function onJoin(PlayerJoinEvent $event){
		$this->wandSessions[$event->getPlayer()->getID()] = self::W_NOTHING;
	}
	public function onQuit(PlayerQuitEvent $event){
		if(isset($this->wandSessions[$k = $event->getPlayer()->getID()])){
			unset($this->wandSessions[$k]);
		}
	}
	public function onInteract(PlayerInteractEvent $event){
		$p = $event->getPlayer();
		switch($this->wandSessions[$p->getID()]){
			case self::W_NOTHING:
				return;
		}
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
		$this->selectedPoints[$player->getID()] = $pos;
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
		$this->selections[$player->getID()] = $space;
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
