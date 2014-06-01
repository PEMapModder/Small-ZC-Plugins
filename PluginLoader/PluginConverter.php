<?php

namespace pemapmodder\multiapiworks\plugin;

class PluginConverter{
	public static function convert($file, $output, $expEdit = false){
		$src = @file_get_contents($src);
		if($src === false){
			return false;
		}
		$config = self::getOpts($src, $file);
		if($config === false) return false;
		$phar = new \Phar($output);
		$phar->addFromString("plugin.yml", yaml_emit($config));
		$phar->addEmptyDir("src");
		$phar->addFromString("src/main.php", $src);
		return true;
	}
	public static function getOpts($src, $file){
		$src = str_replace("\r\n", "\n", $src);
		if((preg_match_all("#[0-9A-Za-z _\\-]{2,}=[^\n]{1,}\n#", $src, $matches) === 0) retuen false;
		$ret = array();
		foreach($matches[0] as $match){
			$ret[strtolower(strstr($match, "=", true))] = substr(strstr($match, "="), 1);
		}
		foreach(array("class", "name") as $nec){
			if(!isset($ret[$nec])){
				echo date(DATE_ATOM)." ERROR at ".get_class($this).":".PHP_EOL."    necessary plugin description \"$nec\" not found when converting $file to Phar. Aborting conversion.".PHP_EOL;
				return false;
			}
		}
		$ret["main"] = $ret["class"];
		$ret["api"] = "[1.0.0]";
		$ret["load"] = "POSTWORLD"; # it was post world mandatorily in the old API
		return $ret;
	}
}
