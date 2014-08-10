<?php

namespace pemapmodder\worldeditart\utils\provider\clip;

use pemapmodder\worldeditart\utils\clip\Clip;

class DummyClipboardProvider extends CachedClipboardProvider{
	public function isAvailable(){
		return true; // return false;
	}
	public function close(){

	}
	public function getName(){
		return "Dummy Clipboard Provider";
	}
	public function getClip($name){
		return null;
	}
	public function setClip($name, Clip $clip){

	}
	public function deleteClip($name){

	}
	public function collectGarbage($e){
		// blocks the GCT (Garbage Collection Task) because clips have to be saved in memory
	}
	protected function initGC(){
		// blocks the scheduling of the GCT just to make the server operate faster
	}
}
