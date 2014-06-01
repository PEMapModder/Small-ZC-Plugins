<?php

namespace pemapmodder\invgui;

use pocketmine\Player;
use pocketmine\command\Command;
use pocketmine\command\CommandExecutor as CmdExe;
use pocketmine\command\CommandSender as Isr;
use pocketmine\command\PluginCommand as Cmd;
use pocketmine\event\Event;
use pocketmine\event\EventExecutor as EvtExe;
use pocketmine\event\EventPriority as EvtPrty;
use pocketmine\event\Listener;
use pocketmine\item\Item;
use pocketmine\network\protocol\ContainerSetContentPacket;
use pocketmine\utils\TextFormat as Font;

class Main extends pocketmine\plugin\PluginBase implements CmdExe, Listener, EvtExe{
	public static $inst = false;
	public $sessions = array();
	public $guiMap = false;
	public function onLoad(){
		self::$inst = $this;
	}
	public function getMap(){
		return $this->guiMap;
	}
	public function onEnable(){
		$this->guiMap = new GuiMap();
		$cmd = new Cmd("opts", $this);
		$cmd->setAliases(array("invgui", "ig"));
		$cmd->setDescription("Toggles/checks your InventoryGUI status");
		$cmd->setUsage("[on|off]");
		$cmd->register($this->getServer()->getCommandMap());
		$this->getServer()->getPluginManager()->registerEvent("pocketmine\\event\\player\\PlayerItemHeldEvent", $this, EvtPrty::HIGHEST, $this, $this, true); // so many $this's...
	}
	public function onCommand(CommandSender $isr, Command $cmd, $l, array $args){
		if(!($isr instanceof Player)) return false;
		if(($ia = strtolower($args[0])) === "on"){
			$this->sendGuiInventory($this->guiMap, $isr);
			$this->sessions[$isr->getName()] = array();
			$isr->sendMessage("Your InvGui status is now ON.");
			// $this->getServer()->getScheduler()->scheduleDelayedTask(new OpenGuiInvTask($this, $isr), 20);
			return true;
		}
		if($ia === "off"){
			$this->sessions[$isr->getName()] = false;
			$isr->sendInventory();
			$isr->sendMessage("Your InvGui status is now OFF.");
			return true;
		}
		$isr->sendMessage("Your InvGui status is now ".($this->sessions[$isr->getName()] ? "ON":"OFF").".");
		return true;
	}
	public function sendGuiInventory(GuiMap $map, Player $player){
		$inv = array();
		foreach($map->priority as $id=>$p)
			$inv[] = Item::get($id & 0x1FF, ($id >> 9) & 0x0F);
		$pk = new ContainerSetContentPacket;
		$pk->windowId = 0;
		$pk->slots = $inv;
		$pk->hotbar = array(0);
		for($i = 0; $i < 7; $i++)
			$pk->hotbar[] = -1;
		$player->dataPacket($pk);
	}
	public function execute(Listener $me, Event $evt){ // $me? LOL
		switch(substr(array_slice(explode("\\", get_class($evt)), -1)[0], 0, -5)){
			case "PlayerItemHeld":
				$s = $this->sessions[$evt->getPlayer()->getName()];
				if($s === false)
					break;
				$evt->setCancelled(true);
				$item = $evt->getItem();
				$gui = $this->guiMap->getSubmap($s)->map[($item->getMetadata() << 9) | ($item->getID() & 0x1FF)];
				$restore = $gui->onClicked();
				if($gui->isParent()){
					$s[] = $gui->getID();
					$this->sessions[$evt->getPlayer()->getName()] = $s;
					$this->sendGuiInventory($this->guiMap->getSubmap($s), $evt->getPlayer());
				}
				elseif($gui->preventHarm() !== true){
					$evt->getPlayer()->harm(1, "gui.harm.invscreen.close.force"); // TODO WARNING this method has not been implemented yet. Watch pocketmine\entity\Damageable.
					$evt->getPlayer()->heal(1, "gui.harm.invscreen.close.force.restore"); // TODO WARNING this method has not been implemented yet. Watch pocketmine\entity\Damageable.
				}
				if($restore === true)
					$this->onCommand($evt->getPlayer(), null, "", array("off"));
				break;
		}
	}
	public static function register(InvGui $gui){
		$ih = $gui->getInheritance();
		$map = self::$inst->getMap();
		$map = $map->getSubmap($ih);
		$map->map[$gui->getID()] = $gui->getPriority();
		$map->priority[$gui->getID()] = $gui->getPriority();
		rsort($map->priority, SORT_NUMERIC);
		if(count($map->map) >36){
			$id = array_keys($map->priority)[36];
			$item = Item::get($id & 0x1FF, ($id >> 9) & 0x0F);
			console("[WARNING] More than 36 InvGUIs registered in a single map! Truncating the one with the lowest priority, ".Font::AQUA."$item".Font::YELLOW."!");
			unset($map->map[array_keys($map->priority)[36]]);
			unset($map->priority[array_keys($map->priority)[36]]);
		}
		else console(Font::GREEN."[INFO] New InvGUI ".Font::AQUA."$gui".Font::GREEN." registered!"/*, true, true, 2*/);
	}
}
