<?php

namespace pemapmodder\clb;

use pocketmine\plugin\Plugin;
use pocketmine\scheduler\PluginTask;

class CallbackTask extends PluginTask{
	/** @var callable $callback */
	private $callback;
	/** @var mixed[] $args */
	private $args;

	public function __construct(Plugin $plugin, callable $callback, array $args){
		parent::__construct($plugin);
		$this->callback = $callback;
		$this->args = $args;
	}

	public function onRun($ticks){
		call_user_func_array($this->callback, $this->args);
	}
}
