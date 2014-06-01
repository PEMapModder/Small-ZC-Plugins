<?php

namespace pemapmodder\pluginloader;

use pocketmine\Server;
use pocketmine\command\Command;
use pocketmine\command\CommandSender as Isr;
use pocketmine\command\PluginCommand as Cmd;

class YamlPluginBase implements Plugin{
	protected $src;
	public function __construct($yaml){
		$this->server = Server::getInstance();
		$this->src = \yaml_parse(str_replace("\t", "    ", $yaml));
		$this->init();
	}
	protected function init(){
		foreach($this->src["commands"] as $command=>$data){
			$cmd = new Cmd($command, $this);
			foreach(array("description", "usage", "aliases", "permission") as $a){
				eval("\$"."$a = isset(\$data[\"$a\"]) ? \$data[\"$a\"]:\"\";");
				eval("\$cmd->set".ucfirst($a)."(\$"."$a);");
			}
			if(isset($data["actions"]))
				$this->cmds[$command] = $data["actions"];
			else{
				\console("[ERROR] Plugin {$this->src["name"]} command /$command contains no actions on register. Aborting registration.", true, true, 0);
				continue;
			}
			$cmd->register($this->server->getCommandMap());
		}
	}
	public function onCommand(Isr $isr, Command $cmd, $label, array $args){
		
	}
	public function evalActionLine($line){
	}
}

								
