<?php

namespace pemapmodder\dst;

use pocketmine\math\Vector3;
use pocketmine\scheduler\PluginTask;
use pocketmine\tile\Sign;

class TickUpdater extends PluginTask{
	public function onRun($ticks){
		/** @var Main $main */
		$main = $this->getOwner();
		$dels = 0;
		foreach($main->getServer()->getLevels() as $level){
			$db = $main->getDb($level);
			$op = $db->prepare("SELECT * FROM signs WHERE (:ticks % intv) = 0;");
			$op->bindValue(":ticks", $ticks); // didn't know I would ever use this :D
			$result = $op->execute();
			while(($data = $result->fetchArray(SQLITE3_ASSOC)) !== false){
				$tile = $level->getTile(new Vector3($data["x"], $data["y"], $data["z"]));
				if($tile instanceof Sign){
					$lengths = $data["lengths"];
					$text = $data["texts"];
					$texts = [];
					$offset = 0;
					for($i = 0; $i < strlen($lengths) * 2; $i++){
						$texts[] = substr($text, $offset, $length = $this->getHalfByteValue($lengths, $i));
						$offset += $length;
					}
					$last = count($texts) - 1;
					if($texts[$last] === ""){
						unset($texts[$last]);
					}
					while(($count = count($texts)) % 4 !== 0){
						$texts[] = "";
					}
					$scroll = $data["scrolltype"];
					$pages = ceil($count / $scroll);
					$curPage = $ticks % ($data["intv"] * $pages);
					$cur = array_slice($texts, $curPage * $scroll, 4);
					$tile->setText($cur[0], $cur[1], $cur[2], $cur[3]);
				}
				else{
					$op = $db->prepare("DELETE FROM signs WHERE id = :id;");
					$op->bindValue(":id", $data["id"]);
					$op->execute();
					$dels++;
				}
			}
		}
		if($dels){
			$main->getLogger()->notice(($dels === 1 ? "A dynamic sign has":"$dels dynamic signs have")." been unregistered due to being removed.");
		}
	}
	public static function getHalfByteValue($string, $offset){
		$back = (bool) ($offset % 2);
		$char = substr($string, $offset >> 1, 1);
		$ord = ord($char);
		return ($back ? $ord & 0x0F : $ord >> 4);
	}
}
