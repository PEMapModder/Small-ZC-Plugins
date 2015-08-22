<?php

namespace pemapmodder\worldeditart;

use pemapmodder\worldeditart\events\AnchorChangeEvent;
use pemapmodder\worldeditart\events\SelectionChangeEvent;
use pemapmodder\worldeditart\utils\clip\Clip;
use pemapmodder\worldeditart\utils\macro\Macro;
use pemapmodder\worldeditart\utils\provider\clip\BinaryClipboardProvider;
use pemapmodder\worldeditart\utils\provider\clip\DummyClipboardProvider;
use pemapmodder\worldeditart\utils\provider\clip\MysqliClipboardProvider;
use pemapmodder\worldeditart\utils\provider\macro\DummyMacroDataProvider;
use pemapmodder\worldeditart\utils\provider\macro\LocalNBTMacroDataProvider;
use pemapmodder\worldeditart\utils\provider\macro\MysqliMacroDataProvider;
use pemapmodder\worldeditart\utils\provider\player\DummyPlayerDataProvider;
use pemapmodder\worldeditart\utils\provider\player\JSONFilePlayerDataProvider;
use pemapmodder\worldeditart\utils\provider\player\MysqliPlayerDataProvider;
use pemapmodder\worldeditart\utils\provider\player\PlayerData;
use pemapmodder\worldeditart\utils\provider\player\SQLite3PlayerDataProvider;
use pemapmodder\worldeditart\utils\provider\player\YAMLFilePlayerDataProvider;
use pemapmodder\worldeditart\utils\spaces\CylinderSpace;
use pemapmodder\worldeditart\utils\spaces\Space;
use pemapmodder\worldeditart\utils\subcommand\Anchor;
use pemapmodder\worldeditart\utils\subcommand\Copy;
use pemapmodder\worldeditart\utils\subcommand\Cuboid;
use pemapmodder\worldeditart\utils\subcommand\Cut;
use pemapmodder\worldeditart\utils\subcommand\Cylinder;
use pemapmodder\worldeditart\utils\subcommand\Desel;
use pemapmodder\worldeditart\utils\subcommand\MacroSubcmd as MacroSubcommand;
use pemapmodder\worldeditart\utils\subcommand\Paste;
use pemapmodder\worldeditart\utils\subcommand\PosSubcommand;
use pemapmodder\worldeditart\utils\subcommand\Replace;
use pemapmodder\worldeditart\utils\subcommand\SelectedToolSetterSubcommand;
use pemapmodder\worldeditart\utils\subcommand\Set;
use pemapmodder\worldeditart\utils\subcommand\Sphere;
use pemapmodder\worldeditart\utils\subcommand\SubcommandMap;
use pemapmodder\worldeditart\utils\subcommand\Test;
use pocketmine\block\Air;
use pocketmine\block\Block;
use pocketmine\entity\Entity;
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\block\BlockPlaceEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\event\server\DataPacketReceiveEvent;
use pocketmine\level\Position;
use pocketmine\math\Vector3;
use pocketmine\network\protocol\UseItemPacket;
use pocketmine\Player;
use pocketmine\plugin\PluginBase;

const IS_DEBUGGING = true;

class WorldEditArt extends PluginBase implements Listener{
	public function onEnable(){
		$this->getServer()->getPluginManager()->registerEvents($this, $this);
		$this->saveDefaultConfig();
	}
	public function getMaxUndoQueue(){
		return $this->getConfig()->getNested("undo-queue.max-size", 5);
	}
}
