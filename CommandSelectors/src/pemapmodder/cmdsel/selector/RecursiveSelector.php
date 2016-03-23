<?php

namespace pemapmodder\cmdsel\selector;

use pocketmine\command\CommandSender;
use pocketmine\Server;

interface RecursiveSelector{
	/**
	 * @return string
	 */
	public function getName();

	/**
	 * @return string[]
	 */
	public function getAliases();

	/**
	 * @param Server        $server
	 * @param CommandSender $sender
	 * @param               $name
	 * @param array         $args
	 *
	 * @return string[]|bool
	 */
	public function format(Server $server, CommandSender $sender, $name, array $args);
}
