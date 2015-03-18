<?php

namespace authtools\action;

interface Action{
	public function getName();
	public function getMessage();
	public function getInputType();
	public function getSuccessActions();
	public function getFailrueActions();
}
