<?php

namespace authtools\action;

use authtools\AuthTools;
use authtools\input\InputHandler;

class ConfiguredAction implements Action{
	/** @var string */
	private $name;
	private $message;
	private $input;
	private $success;
	private $failure;
	private $inputName;
	private $successNames;
	private $failureNames;
	public function __construct($name, array $args){
		$this->name = $name;
		$this->message = $args["message"];
		$this->input = $args["input"];
		$this->successNames = $args["success"];
		$this->failureNames = $args["failure"];
	}
	public function getName(){
		return $this->name;
	}
	public function validate(AuthTools $main){
		$input = $main->getInputHandler($this->inputName);
		if(!($input instanceof InputHandler)){
			$main->getLogger()->critical("InputHandler \"$this->inputName\" not found, using \"none\" instead!");
			$input = $main->getInputHandler("none");
		}
		foreach($this->successNames as $name){
			$action = $main->getAction($name);
			if(!($action instanceof Action)){
				$main->getLogger()->warning("Action \"$name\" cannot be found!");
			}else{
				$this->success[] = $action;
			}
		}
		foreach($this->failureNames as $name){
			$action = $main->getAction($name);
			if(!($action instanceof Action)){
				$main->getLogger()->warning("Action \"$name\" cannot be found!");
			}else{
				$this->failure[] = $action;
			}
		}
	}
	public function getMessage(){
		return $this->message;
	}
	public function getInputType(){
		return $this->input;
	}
	public function getSuccessActions(){
		return $this->success;
	}
	public function getFailrueActions(){
		// TODO: Implement getFailrueActions() method.
	}
}
