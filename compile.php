<?php
$statusPadding = 0;
preg_match_all("/Columns:[ \t]+([0-9]+)/", `mode`, $matches);
$MAX_LENGTH = ((int) $matches[1][0]) - 1;

$MAX_LENGTH = 180;

$input = trim(readLine("Plugin dir name: "), "/\\");
if(!is_dir($input)){
	fail("Directory doesn't exist!");
}
$dir = rtrim(realpath($input), "/\\") . "/";
if(!is_file($pluginYml = $dir . "plugin.yml")){
	fail("Plugin manifest not found!");
}
if(!is_dir($srcDir = $dir . "src/")){
	fail("Source folder not found!");
}
if(!is_dir($dir . "bin")){
	console("Creating bin/ directory and its README file...");
	mkdir($dir . "bin");
	file_put_contents($readme = $dir . "bin/README.md", <<<EOF
$input/bin
===
This directory stores a .phar build of the latest built version, but not necessarily the latest commit.
EOF
);
}
if($hasRes = is_dir($resDir = $dir . "resources/")){
	console("Resources directory detected.");
}

$manifest = yaml_parse_file($pluginYml);
$version = $manifest["version"];
$last3 = substr($version, -3);
if(!is_numeric($last3)){
	$last3 = 0;
	$version .= "_#";
}else{
	$version = substr($version, 0, -3);
}
$last3 = ((int) $last3) + 1;
$version .= str_pad((string) $last3, 3, "0", STR_PAD_LEFT);
$manifest["version"] = $version;
yaml_emit_file($pluginYml, $manifest);
console("New $input version: $version");

$path = $dir . "bin/" . $input . ".phar";
if(is_file($path)){
	console("Older phar detected, deleting...");
	unlink($path);
}
console("Creating phar at $path...");
$phar = new Phar($path);
$phar->setStub('<?php __HALT_COMPILER();');
$phar->setSignatureAlgorithm(Phar::SHA1);
$phar->startBuffering();
console("Adding files into phar...");
console("Adding plugin.yml...");
$phar->addFile($pluginYml, "plugin.yml");

clearLine();
console("Adding sources...");
$cnt = 0;
foreach(new RecursiveIteratorIterator(new RecursiveDirectoryIterator($srcDir)) as $absPath){
	if(!is_file($absPath)){
		continue;
	}
	$cnt++;
	$absPath = realpath($absPath);
	status("[$cnt] $absPath");
	$relPath = ltrim(substr($absPath, strlen($dir)), "/\\");
	$phar->addFile($absPath, $relPath);
}

if($hasRes){
	clearLine();
	console("Adding resources...");
	$cnt = 0;
	foreach(new RecursiveIteratorIterator(new RecursiveDirectoryIterator($resDir)) as $absPath){
		if(!is_file($absPath)){
			continue;
		}
		$cnt++;
		$absPath = realpath($absPath);
		status("[$cnt] $absPath");
		$relPath = ltrim(substr($absPath, strlen($dir)), "/\\");
		$phar->addFile($absPath, $relPath);
	}
}

console("Compressing files...");
$phar->compressFiles(Phar::GZ);
$phar->stopBuffering();

console("Done! Phar created at \x1b[33;1m$path\x1b[0m.");
console("Staging generated/modified files into Git..");
echo `git add -A :/`;

fail("Everything finished! Press enter to close this window \x1b[32;1m\x1b[4m:)\x1b[0m", 1);

function readLine($prompt = ""){
	echo $prompt;
	while(!($line = trim(fgets(fopen("php://stdin", "rt")))));
	return $line;
}
function fail($reason, $code = 2){
	echo $reason . PHP_EOL;
	exit($code);
}
function console($msg){
	clearLine();
	echo $msg;
	echo PHP_EOL;
}
function status($msg){
	global $statusPadding;
	clearLine();
	echo $msg;
	echo "\r";
	$statusPadding = strlen($msg);
}
function clearLine(){
	global $statusPadding, $MAX_LENGTH;
	echo str_repeat(" ", $MAX_LENGTH);
	echo "\r";
	$statusPadding = 0;
}
