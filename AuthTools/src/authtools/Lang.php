<?php

namespace authtools;

class Lang{
	const RES_NAME = "lang.properties";
	/** @var AuthTools */
	private $main;
	/** @var string[] */
	private $config, $defaultConfig;
	public function __construct(AuthTools $main){
		$this->main = $main;
		$data = stream_get_contents($rh = $main->getResource(self::RES_NAME));
		fclose($rh);
		$out = $main->getDataFolder() . self::RES_NAME;
		if(!file_exists($out)){
			file_put_contents($out, $data);
			$this->config = $this->defaultConfig = $this->parseProperties($data);
		}else{
			$this->config = $this->parseProperties(file_get_contents($out));
			$this->defaultConfig = $this->parseProperties($data);
		}
	}
	public function __get($k){
		if(isset($this->config[$k])){
			return $this->config[$k];
		}
		$this->main->getLogger()->warning("Missing key in user language file: $k");
		if(isset($this->defaultConfig[$k])){
			return $this->defaultConfig[$k];
		}
		$this->main->getLogger()->error("Missing key in default language file: '$k'. Please report this issue at https://github.com/PEMapModder/Small-ZC-Plugins/issues/ to the developer if it hasn't been already reported.");
		return $k;
	}
	/**
	 * @link https://github.com/PocketMine/PocketMine-MP/blob/master/src/pocketmine/utils/Config.phpL448-470
	 * @param string $content
	 * @return mixed[]
	 */
	protected function parseProperties($content){
		$array = [];
		if(preg_match_all('/([a-zA-Z0-9\-_\.]*)=([^\r\n]*)/u', $content, $matches) > 0){ //false or 0 matches
			foreach($matches[1] as $i => $k){
				$v = trim($matches[2][$i]);
				switch(strtolower($v)){
					case "on":
					case "true":
					case "yes":
						$v = true;
						break;
					case "off":
					case "false":
					case "no":
						$v = false;
						break;
				}
				if(isset($this->config[$k])){
					$this->main->getLogger()->debug("[Config] Repeated property " . $k . " on file will be overwritten");
				}
				$array[$k] = $v;
			}
		}
		return $array;
	}
	public function _($key, $argsMap = []){
		$msg = $this->__get($key);
		$closure = function($key){
			return '$$' . $key;
		};
		return str_replace(array_map($closure, array_keys($argsMap)), array_values($argsMap), $msg);
	}
}
