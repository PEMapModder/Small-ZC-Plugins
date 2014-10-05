<?php

namespace pemapmodder\nailedkeyboard;

use pocketmine\block\SignPost;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerChatEvent;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\event\Timings;
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
			case "SHIFT":
			case "SEL":
			case "SELECT":
				$this->lines[$this->offset($player)]->startSelection();
				break;
			case "DESEL":
				try{
					$this->lines[$this->offset($player)]->deselect();
				}
				catch(\UnexpectedValueException $e){
					return "You don't have a selection to select.";
				}
			case "COPY":
				try{
					$this->lines[$this->offset($player)]->copy();
				}
				catch(\UnexpectedValueException $e){
					return "You are not selecting text! Select a text to copy.";
				}
				break;
			case "CUT":
				try{
					$this->lines[$this->offset($player)]->cut();
				}
				catch(\UnexpectedValueException $e){
					return "You are not selecting text! Select a text to cut.";
				}
				break;
			case "PASTE":
				try{
					$this->lines[$this->offset($player)]->paste();
				}
				catch(\UnexpectedValueException $e){
					return "You don't have a copied text!";
				}
				break;
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
			case "HOME":
				$line->home();
				break;
			case "END":
				$line->end();
				break;
			case "SUBMIT":
			case "ENTER":
			case "SEND":
				$text = $line->getText();
				$line->reset();
				$this->getServer()->getPluginManager()->callEvent($ev = new PlayerCommandPreprocessEvent_sub($player, $text, $this));
				if($ev->isCancelled()){
					return "";
				}
				$text = $ev->getMessage();
				if(substr($text, 0, 1) === "/"){
					Timings::$playerCommandTimer->startTiming();
					$this->getServer()->dispatchCommand($ev->getPlayer(), substr($text, 1));
					Timings::$playerCommandTimer->stopTiming();
					return "";
				}
				$this->getServer()->getPluginManager()->callEvent($ev = new PlayerChatEvent($ev->getPlayer(), $text));
				if(!$ev->isCancelled()){
					$this->getServer()->broadcastMessage(sprintf($ev->getFormat(), $ev->getPlayer()->getDisplayName(), $ev->getMessage()), $ev->getRecipients());
				}
				return "";
			case "VIEW":
				break;
			default:
				$line->input($texts[1]);
				break;
		}
		return "Text: {$line->getText()}\nPointer at \"|\": {$line->getLeftText()}|{$line->getRightText()}" .
			(($selected = $line->getSelectedText()) === null ? "":"\nSelected text: $selected") .
			(($clip = $line->getClipboard()) === null ? "":"\nCopied text: $clip");
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
