<?php

namespace pemapmodder\worldeditart;

use pemapmodder\worldeditart\utils\MyPluginCommand;
use pocketmine\permission\DefaultPermissions;
use pocketmine\permission\Permission;
use pocketmine\Player;
use pocketmine\plugin\PluginBase;
use pemapmodder\worldeditart\utils\spaces\Space;

class Main extends PluginBase{
	/** @var Space[] $sels indexed with player entity IDs */
	private $sels = [];
	public function onEnable(){
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

	}
}
