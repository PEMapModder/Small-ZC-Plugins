<?php

namespace pemapmodder\worldeditart\utils\subcommand;

use pemapmodder\worldeditart\Main;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\command\PluginIdentifiableCommand;
use pocketmine\utils\TextFormat;

const IS_DEBUGGING = \pemapmodder\worldeditart\IS_DEBUGGING;

class SubcommandMap extends Command implements PluginIdentifiableCommand{
	/** @var Main */
	private $main;
	/** @var \WeakRef[] */
	private $subcmds = [];
	/**
	 * @param string $name
	 * @param Main $main
	 * @param string $desc
	 * @param string $mainPerm
	 * @param string[]|string $aliases
	 */
	public function __construct($name, Main $main, $desc, $mainPerm, $aliases = []){
		$this->main = $main;
		parent::__construct($name, $desc, null, (array) $aliases);
		$this->setPermission($mainPerm);
	}
	public function registerSubcommand(Subcommand $subcmd){
		$this->subcmds[strtolower(trim($subcmd->getName()))] = new \WeakRef($subcmd);
		$this->subcmds[strtolower(trim($subcmd->getName()))]->acquire();
		foreach($subcmd->getAliases() as $alias){
			$this->subcmds[$alias] = new \WeakRef($subcmd);
		}
	}
	public function getPlugin(){
		return $this->main;
	}
	public function execute(CommandSender $issuer, $lbl, array $args){
		if(count($args) === 0){
			$args = ["help"];
		}
		if(is_string($lbl) and substr($lbl, 0, 1) === "/" and strlen($lbl) > 1){
			$cmd = substr($lbl, 1);
		}
		else{
			$cmd = array_shift($args);
		}
		if(isset($this->subcmds[$cmd = strtolower(trim($cmd))]) and $this->subcmds[$cmd]->valid() and $cmd !== "help"){
			if($this->subcmds[$cmd]->get()->hasPermission($issuer)){
				if(!IS_DEBUGGING){
					try{
						$this->subcmds[$cmd]->get()->run($this, $args, $issuer);
					}
					catch(\Exception $exception){
						$issuer->sendMessage("Uh-oh. Something went wrong! An exception has been caught during executing your command.");
						$issuer->sendMessage("Error caught: ".($class = array_slice(explode("\\", get_class($exception)), -1)[0]));
						$issuer->sendMessage("Error message: ".$exception->getMessage());
						$issuer->sendMessage("The error has been reported to console.");
						$this->main->getLogger()->notice("An exception has been caught. Exception name: '$class'. Exception message: ".$exception->getMessage());
					}
				}
				else{
					$this->subcmds[$cmd]->get()->run($this, $args, $issuer); // let the error fly
				}
			}
			else{
				$issuer->sendMessage("You either have to select an anchor / make a selection first, don't have permission to do this, or you have to run this command in-game/on-console.");
			}
		}
		else{
			$help = $this->getFullHelp($issuer);
			$page = 1;
			$max = (int) ceil(count($help) / 5);
			if(isset($args[0])){
				$page = max(1, (int) $args[0]);
				$page = min($max, $page);
			}
			$output = "Commands available for you currently: (page $page of $max)\n";
			for($i = ($page - 1) * 5; $i < ($page) * 5 and isset($help[$i]); $i++){
				$output .= $help[$i] . "\n";
			}
			$issuer->sendMessage($output);
		}
		return true;
	}
	/**
	 * @param CommandSender $sender
	 * @return array
	 */
	public function getFullHelp(CommandSender $sender){
		$out = [];
		foreach($this->subcmds as $name => $cmd){
			if($cmd->get()->getName() !== $name){
				continue;
			}
			if(!$cmd->get()->hasPermission($sender)){
				continue;
			}
			$output = "";
			$output .= TextFormat::RESET."/{$this->getName()} ";
			$output .= TextFormat::LIGHT_PURPLE.$cmd->get()->getName()." ";
			$output .= TextFormat::GREEN.$cmd->get()->getUsage()." ";
			$output .= TextFormat::AQUA.$cmd->get()->getDescription();
			$out[] = $output;
		}
		return $out;
	}
	/**
	 * @param Subcommand[] $subcmds
	 */
	public function registerAll(array $subcmds){
		foreach($subcmds as $subcmd){
			$this->registerSubcommand($subcmd);
		}
		$aliases = [];
		foreach($this->subcmds as $name => $sub){
			$aliases[] = "/$name";
		}
		$aliases = array_merge($aliases, $this->getAliases());
		ksort($aliases, SORT_FLAG_CASE | SORT_NATURAL);
		$this->setAliases($aliases);

	}
}
