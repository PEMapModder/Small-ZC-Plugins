<?php

/*
 * DevTools plugin for PocketMine-MP
 * Copyright (C) 2014 PocketMine Team <https://github.com/PocketMine/SimpleAuth>
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
*/

$opts = getopt("", array("relative:", "entry:"));

if(ini_get("phar.readonly") == 1){
	echo "Set phar.readonly to 0 with -dphar.readonly=0\n";
	exit(1);
}

$opts["make"] = "..\\..\\";

$plugin_yml = realpath($opts["make"]."plugin.yml");
$ymlData = yaml_parse_file($plugin_yml);
$version = $ymlData["version"];
$v = ltrim(substr($version, -3), "0");
$v = intval($v) + 1;
$v = "$v";
while(strlen($v) < 3){
	$v = "0".$v;
}
$ymlData["version"] = substr($version, 0, -3).$v;
echo "New version! ".$ymlData["version"];
yaml_emit_file($plugin_yml, $ymlData);
exec("git add $plugin_yml");
$folderPath = rtrim(str_replace("\\", "/", realpath($opts["make"])), "/") . "/";
$relativePath = isset($opts["relative"]) ? rtrim(str_replace("\\", "/", realpath($opts["relative"])), "/") . "/" : $folderPath;
$pharName = "..\\NailedKeyboard_dev_build.phar";
if(is_file($pharName)){
	unlink($pharName);
}

if(!is_dir($folderPath)){
	echo $folderPath ." is not a folder\n";
	exit(1);
}

echo "\nCreating ".$pharName."...\n";
$phar = new \Phar($pharName);
if(isset($opts["entry"]) and $opts["entry"] != null){
	$entry = addslashes(str_replace("\\", "/", $opts["entry"]));
	echo "Setting entry point to ".$entry."\n";
	$phar->setStub('<?php require("phar://". __FILE__ ."/'.$entry.'"); __HALT_COMPILER();');
}else{
	$phar->setStub('<?php __HALT_COMPILER();');
}

$phar->setSignatureAlgorithm(\Phar::SHA1);
$phar->startBuffering();
echo "Adding files...\n";
$maxLen = 0;
$count = 0;
foreach(new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($folderPath)) as $file){
	$path = rtrim(str_replace(array("\\", $relativePath), array("/", ""), $file), "/");
	if($path{0} === "." or strpos($path, "/.") !== false){
		continue;
	}
	if(strpos($path, "bin") !== false){
		continue;
	}
	if(strpos($path, ".md") !== false){
		continue;
	}
	if(strpos($path, ".cmd") !== false){
		continue;
	}
	if(strpos($path, ".lnk") !== false){
		continue;
	}
	$phar->addFile($file, $path);
	if(strlen($path) > $maxLen){
		$maxLen = strlen($path);
	}
	echo "\r[".(++$count)."] ".str_pad($path, $maxLen, " ");
}
echo "\nCompressing...\n";
$phar->compressFiles(\Phar::GZ);
$phar->stopBuffering();

$pharName = realpath($pharName);
echo "\x1b[36;1mDone! Phar created at \x1b[33;1m$pharName\x1b[36;1m.\n";
exec("git add ".$pharName);
if(is_file("copy.php")){
	include "copy.php";
}
exec("pause");
