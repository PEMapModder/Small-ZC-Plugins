<?php

namespace pemapmodder\worldeditart;

use pemapmodder\worldeditart\utils\Macro;
use pemapmodder\worldeditart\utils\MyPluginCommand;
use pemapmodder\worldeditart\utils\spaces\CuboidSpace;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\item\Item;
use pocketmine\level\Position;
use pocketmine\metadata\MetadataValue;
use pocketmine\permission\DefaultPermissions;
use pocketmine\permission\Permission;
use pocketmine\Player;
use pocketmine\plugin\PluginBase;
use pemapmodder\worldeditart\utils\spaces\Space;
use pocketmine\utils\Config;

class Main extends PluginBase{
	/** @var Config */
	private $userConfig;
	/** @var Space[] $sels indexed with player entity IDs */
	private $sels = [];
	/** @var Macro[][] */
	private $macros = [];
	/** @var string[] */
	private $macroSessions = [];
	/** @var Position[] */
	private $refPts = [];
	const WAND_NULL = 0;
	const WAND_SEL_1 = 1;
	const WAND_SEL_2 = 2;
	const WAND_SEL_CIRCLE = 3;
	const WAND_MACRO_SETPOS = 8;
	/** @var int[] $wandSessions actions to do when the player uses the wand, keys in player entity IDs, values in integer constants of Main::WAND_** */
	private $wandSessions = [];
	public function onEnable(){
		@mkdir($this->getDataFolder()."wands/");
		$this->userConfig = new Config($this->getDataFolder()."config.yml", Config::YAML, [
			"default-wand-type" => "292",
		]);
		//// permission ////
		$wea = DefaultPermissions::registerPermission(new Permission("wea", "Allow using WorldEditArt commands", Permission::DEFAULT_OP));
		$cb = DefaultPermissions::registerPermission(new Permission("wea.clipboard", "Allow using clipboard-related WEA commands", Permission::DEFAULT_OP), $wea);
		DefaultPermissions::registerPermission(new Permission("wea.clipboard.cut", "Allow using /cut", Permission::DEFAULT_OP), $cb);
		DefaultPermissions::registerPermission(new Permission("wea.clipboard.copy", "Allow using /copy", Permission::DEFAULT_OP), $cb);
		DefaultPermissions::registerPermission(new Permission("wea.clipboard.paste", "Allow using /paste", Permission::DEFAULT_OP), $cb);
		$macro = DefaultPermissions::registerPermission(new Permission("wea.macro", "Allow using /macro", Permission::DEFAULT_OP), $wea);
		//// command ////
		// /cut
		$cmd = new MyPluginCommand("cut", $this, array($this, "cutCmd"));
		$cmd->setUsage("/cut");
		$cmd->setDescription("Cut a selected space");
		$cmd->setPermission("wea.clipboard.cut");
		$this->getServer()->getCommandMap()->register("wea", $cmd);
		// /copy
		$cmd = new MyPluginCommand("copy", $this, array($this, "copyCmd"));
		$cmd->setUsage("/copy");
		$cmd->setDescription("Copy a selected space");
		$cmd->setPermission("wea.clipboard.copy");
		$this->getServer()->getCommandMap()->register("wea", $cmd);
		// /paste
		$cmd = new MyPluginCommand("paste", $this, array($this, "pasteCmd"));
		$cmd->setUsage("/paste");
		$cmd->setDescription("Paste the copied space");
		$cmd->setPermission("wea.clipboard.paste");
		$this->getServer()->getCommandMap()->register("wea", $cmd);
		// /macro
		$cmd = new MyPluginCommand("macro", $this, array($this, "macroCmd"));
		$cmd->setUsage("/macro <start|status|end|run> [number]");
		$cmd->setDescription("Record/run a macro");
		$cmd->setPermission("wea.macro");
		$this->getServer()->getCommandMap()->register("wea", $cmd);
	}
	public function cutCmd($cmd, array $args, Player $player){

	}
	public function copyCmd($cmd, array $args, Player $player){

	}
	public function pasteCmd($cmd, array $args, Player $player){

	}
	public function macroCmd($cmd, array $args, Player $player){
		/** @var Macro[] $macros */
		$macros = isset($this->macros[strtolower($player->getName())]) ? $this->macros[$player->getName()]:false;
		if(!isset($args[0])){
			return false;
		}
		$sc = array_shift($args);
		switch($sc){
			case "setref":
				if($this->macroSessions[$this->getSID($player)] !== false){
					return "You cannot change a selection while recording a macro!";
				}
				if(isset($args[0])){
					switch($ssc = array_shift($args)){
						case "here":
							$this->refPts[$this->getSID($player)] = new Position($player->getFloorX(), $player->getFloorY(), $player->getFloorZ(), $player->getLevel());
							$player->sendMessage("Your macro reference point has been set to (".$player->getFloorX().", ".$player->getFloorY().
								", ".$player->getFloorZ().") in world \"".$player->getLevel()->getName()."\".");
							break;
						case "wand":
							$this->wandSessions[$this->getSID($player)] = self::WAND_MACRO_SETPOS;
							return "Tap a block with your wand to select the macro reference point.";
					}
				}
				return "Usage: /$cmd setref here|wand";
			case "start":
				if(!isset($args[0])){
					return "Please provide a name. Usage: /$cmd start <name>";
				}
				if(!isset($this->refPts[$this->getSID($player)]) or !($this->refPts[$this->getSID($player)] instanceof Position)){
					return "Please select a reference point using /$cmd setref here|wand";
				}
				$name = array_shift($args);
				$macros[$name] = new Macro($player, $this->refPts[$this->getSID($player)]);
				$this->macroSessions[$this->getSID($player)] = $name;
				$this->wandSessions[$this->getSID($player)] = self::WAND_NULL;
				break;
			case "end":
				if(!isset($this->macroSessions[$this->getSID($player)]) or ($name = $this->macroSessions[$this->getSID($player)]) === false){
					return "You don't have a recording macro to stop!";
				}
				$this->macroSessions[$this->getSID($player)] = false;
		}
		return false;
	}
	public function getSID(Player $player){
		return $player->getMetadata("WEA")[0][0];
	}
	public function getSelection(Player $player){
		if(!isset($this->sels[$this->getSID($player)])){
			$this->sels[$this->getSID($player)] = new CuboidSpace(new Position(0, 0, 0, $this->getServer()->getDefaultLevel()), new Position(0, 0, 0, $this->getServer()->getDefaultLevel()));
		}
		return $this->sels[$this->getSID($player)];
	}
	public function onInteract(PlayerInteractEvent $event){
		$player = $event->getPlayer();
		$block = $event->getBlock();
		if($this->isWand($event->getItem(), $player)){
			if($this->wandSessions[$this->getSID($player)] === self::WAND_NULL){
				return;
			}
			$event->setCancelled(true);
			switch($this->wandSessions[$this->getSID($player)]){
				case self::WAND_MACRO_SETPOS:
					$this->refPts[$this->getSID($player)] = $block;
					$player->sendMessage("Your macro reference point has been changed to ({$block->getX()},".
						"{$block->getY()}, {$block->getZ}) at world {$block->getLevel()->getName()}.");
					break;
				case self::WAND_SEL_1:
					break;
				case self::WAND_SEL_2:
					break;
			}
		}
	}
	public function isWand(Item $item, Player $player){
		$matches = [(string) $item->getID(), $item->getID().":".$item->getDamage()];
		if(is_file($file = $this->getDataFolder()."wands/".strtolower($player->getName()).".txt")){
			return in_array(file_get_contents($file), $matches);
		}
		return in_array($this->userConfig->get("default-wand-type"), $matches);
	}
	public function setWand(Player $player, $item = false){
		if(is_string($item)){
			file_put_contents($this->getDataFolder()."wands/".strtolower($player->getName()).".txt", $item);
		}
		@unlink($this->getDataFolder()."wands/".strtolower($player->getName()).".txt");
	}
}
