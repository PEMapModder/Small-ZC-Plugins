<?php

namespace pemapmodder\worldeditart\utils\provider\macro;

use pemapmodder\worldeditart\utils\macro\Macro;

class DummyMacroDataProvider extends CachedMacroDataProvider{
	public function isAvailable(){
		return true; // return false;
	}
	public function close(){

	}
	public function getName(){
		return "Dummy Macro Data Provider";
	}
	public function collectGarbage($e){
		// blocks the GCT (Garbage Collection Task) because macros have to be saved in memory
	}
	protected function initGC(){
		// blocks the scheduling of the GCT just to make the server operate faster
	}
	public function readMacro($name){
		return null;
	}
	public function saveMacro($name, Macro $clip){

	}
	public function deleteMacro($name){

	}
}
