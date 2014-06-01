<?php

namespace pemapmodder\pluginloader;

use pocketmine\plugin\PluginDescription;
use pocketmine\plugin\PluginLoader as PMPL;
use pocketmine\Server;

class MPluginLoader implements PMPL{
	public function __construct(Server $server){
	}
	public function loadPlugin($file){
		
	}
	public function getPluginDescription($file){
		$src=file_get_contents($file);
		// fix EOL in Windows
		$src=preg_replace("/\r[^\n]/", "\n", $src);
		str_replace("\r\n", "\n", $src);
		$lines=explode("\n", $src);
		for($i=0; $i<count($lines-2); $i++){
			if($lines[$i]==="/*" and $lines[$i+1]==="old_plugin"){
				$description="";
				for($j=$i; $j<count($lines); $j++){
					if($lines[$j]!=="*/"){
						$description.=$lines[$j];
						$description.="\n";
						continue;
					}
					break;
				}
				return $this->parseDescription($description);
			}
		}
	}
	private function parseDescription($txt){
		if(preg_match_all("#@[A-Za-z0-9]{1,}([\t ]{1,})=([\t ]{1,})([A-Za-z0-9_\\-]{1,})#", $txt, $matches)<2)return false;
		$args=array();
		foreach($matches[0] as $match){
			$trimmed=str_replace(array("@", "\n"), array("", ""), $match);
			$trimmed=preg_split("/[\t ]{1,}/", $trimmed);
			$key=array_shift($trimmed);
			$value=implode(" ", $trimmed);
			$args[$key]=$value;
		}
		return new PluginDescription(yaml_emit($args)); // so pointless to yaml_emit() and then immediately yaml_parse()...
	}
	public function getPluginFilters(){
		return "/(A-Za-z0-9){1,}\\.php$/";
	}
	public function enablePlugin(){
	}
	public function disablePlugin(){
	}
}
