<?php

namespace pemapmodder\nailedkeyboard;

use pocketmine\block\SignPost;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\network\protocol\MessagePacket;
use pocketmine\Player;
use pocketmine\plugin\PluginBase;
use pocketmine\tile\Sign;

class NailedKeyboard extends PluginBase implements Listener{
	/** @var Line[] */
	private $lines = [];
	public function onEnable(){
		if(!extension_loaded("multibyte")){
			$this->getLogger()->warning("Multibyte extension is not loaded! Moving the text pointer left or right might have some issues!");
		}
		$this->getServer()->getPluginManager()->registerEvents($this, $this);
	}
	/**
	 * @param PlayerInteractEvent $event
	 * @priority LOWEST
	 */
	public function onInteract(PlayerInteractEvent $event){
		$player = $event->getPlayer();
		if($player->hasPermission("nailedkeyboard")){
			if($event->getBlock() instanceof SignPost){
				$sign = $event->getBlock()->getLevel()->getTile($event->getBlock());
				if($sign instanceof Sign){
					$texts = $sign->getText();
					if($texts[0] === "NailedKeyboard"){
						$event->setCancelled();
						if(is_string($reply = $this->handleSignTouch($player, $texts)) and trim($reply) !== ""){
							$player->sendMessage($reply);
						}
					}
				}
			}
		}
	}
	private function handleSignTouch(Player $player, array $texts){
		$fx = strtoupper($texts[2]);
		$line = $this->get($player);
		switch($fx){
			case "LEFT":
				try{
					$line->left();
				}
				catch(\OutOfBoundsException $e){
					return "The pointer is already at the leftmost of the text!";
				}
				break;
			case "RIGHT":
				try{
					$line->right();
				}
				catch(\OutOfBoundsException $e){
					return "The pointer is already at the rightmost of the text!";
				}
				break;
			case "RESET":
				$line->reset();
				break;
			case "BACKSPACE":
				try{
					$line->backspace();
				}
				catch(\OutOfBoundsException $e){
					return "Nothing to delete at the left side!";
				}
				break;
			case "DELETE":
				try{
					$line->delete();
				}
				catch(\OutOfBoundsException $e){
					return "Nothing to delete at the right side!";
				}
				break;
			case "SUBMIT":
			case "ENTER":
			case "SEND":
				$text = $line->getText();
				$line->reset();
				$pk = new MessagePacket;
				$pk->message = $text;
				$pk->source = ""; // this is a redundant but version-secure line
				$player->handleDataPacket($pk);
				break;
			case "VIEW":
				break;
			default:
				$line->input($texts[1]);
				break;
		}
		return "Text: {$line->getText()}\nPointer at \"|\": {$line->getLeftText()}|{$line->getRightText()}";
	}
	/**
	 * @param Player $player
	 * @return int the offset of the keyboard in the array
	 */
	public function touch(Player $player){
		if(!isset($this->lines[$offset = $this->offset($player)])){
			$this->lines[$offset] = new Line;
		}
		return $offset;
	}
	/**
	 * @param Player $player
	 * @return Line
	 */
	public function get(Player $player){
		return $this->lines[$this->touch($player)];
	}
	private function offset(Player $player){
		return $player->getID();
	}
}
