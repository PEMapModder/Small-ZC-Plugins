<?php

namespace authtools;

use authtools\cmd\ChangePasswordCommandExecutor;
use pocketmine\command\PluginCommand;
use pocketmine\Player;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\TextFormat;
use SimpleAuth\SimpleAuth;

class AuthTools extends PluginBase{
	/** @var SimpleAuth */
	public $sa;
	/** @var Lang */
	public $_;
	/** @var Settings */
	private $settings;
	/** @var EventHandler */
	private $eh;
	/** @var AuthToolsSession[] */
	private $sessions = [];
	public function onEnable(){
		$this->sa = $this->getServer()->getPluginManager()->getPlugin("SimpleAuth");
		if(!($this->sa instanceof SimpleAuth)){
			$this->getLogger()->critical("Incorrect SimpleAuth plugin - main not instance of SimpleAuth\\SimpleAuth! Please download the official version of SimpleAuth from PocketMine Forums " . TextFormat::UNDERLINE . "(https://forums.pocketmine.net/plugins/)" . TextFormat::RED . " by @PocketMine Team!");
			$this->getServer()->getPluginManager()->disablePlugin($this);
			return;
		}
		$this->saveDefaultConfig();
		$this->_ = new Lang($this);
		$this->settings = new Settings($this);
		$cmd = $this->getCommand("changepw");
		$cmd->setPermissionMessage($this->_->ChangepwPermMsg);

		if($cmd instanceof PluginCommand){
			$cmd->setExecutor(new ChangePasswordCommandExecutor($this));
		}
		$this->getServer()->getPluginManager()->registerEvents($this->eh = new EventHandler($this), $this);
	}
	public function addSession(Player $player){
		if(!isset($this->sessions[$player->getId()])){
			$this->sessions[$player->getId()] = new AuthToolsSession($player);
		}
		return $this->sessions[$player->getId()];
	}
	public function rmSession(Player $player){
		if(isset($this->sessions[$player->getId()])){
			unset($this->sessions[$player->getId()]);
		}
	}
	public function getSession(Player $player){
		return isset($this->sessions[$player->getId()]) ? $this->sessions[$player->getId()] : null;
	}

	/**
	 * Uses SHA-512 [http://en.wikipedia.org/wiki/SHA-2] and Whirlpool [http://en.wikipedia.org/wiki/Whirlpool_(cryptography)]
	 *
	 * Both of them have an output of 512 bits. Even if one of them is broken in the future, you have to break both of them
	 * at the same time due to being hashed separately and then XORed to mix their results equally.
	 *
	 * @param string $salt
	 * @param string $password
	 *
	 * @return string[128] hex 512-bit hash
	 */
	public static function hash($salt, $password){
		return bin2hex(hash("sha512", $password . $salt, true) ^ hash("whirlpool", $salt . $password, true));
	}
	/**
	 * @return Settings
	 */
	public function getSettings(){
		return $this->settings;
	}
}
