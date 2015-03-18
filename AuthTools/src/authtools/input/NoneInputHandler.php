<?php

namespace authtools\input;

use authtools\AuthenticateSession;

class NoneInputHandler implements InputHandler{
	public function getName(){
		return "none";
	}
	public function handleInput(AuthenticateSession $session, $input){
		return true;
	}
}
