<?php

namespace authtools\input;

use authtools\AuthenticateSession;

interface InputHandler{
	/**
	 * @return string
	 */
	public function getName();
	/**
	 * @param AuthenticateSession $session Session representation for the player who sent the input
	 * @param string $input chat input
	 * @return bool success (true) or failure (false)
	 */
	public function handleInput(AuthenticateSession $session, $input);
}
