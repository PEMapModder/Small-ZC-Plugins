<?php

namespace{
	if(!function_exists("hash_equals")){
		/**
		 * Timing attack safe string comparison
		 *
		 * Compares two strings using the same time whether they're equal or not.
		 * This function should be used to mitigate timing attacks; for instance, when testing crypt() password hashes.
		 *
		 * @author Markus P. N.
		 * @link http://php.net/hash-equals
		 *
		 * @param string $known_string The string of known length to compare against
		 * @param string $user_string The user-supplied string
		 * @return boolean Returns TRUE when the two strings are equal, FALSE otherwise.
		 */
		function hash_equals($known_string, $user_string)
		{
			if (func_num_args() !== 2) {
				// handle wrong parameter count as the native implentation
				trigger_error('hash_equals() expects exactly 2 parameters, ' . func_num_args() . ' given', E_USER_WARNING);
				return null;
			}
			if (is_string($known_string) !== true) {
				trigger_error('hash_equals(): Expected known_string to be a string, ' . gettype($known_string) . ' given', E_USER_WARNING);
				return false;
			}
			$known_string_len = strlen($known_string);
			$user_string_type_error = 'hash_equals(): Expected user_string to be a string, ' . gettype($user_string) . ' given'; // prepare wrong type error message now to reduce the impact of string concatenation and the gettype call
			if (is_string($user_string) !== true) {
				trigger_error($user_string_type_error, E_USER_WARNING);
				// prevention of timing attacks might be still possible if we handle $user_string as a string of diffent length (the trigger_error() call increases the execution time a bit)
				/** @noinspection PhpUnusedLocalVariableInspection */
				$user_string_len = strlen($user_string);
				$user_string_len = $known_string_len + 1;
			} else {
				/** @noinspection PhpUnusedLocalVariableInspection */
				$user_string_len = $known_string_len + 1;
				$user_string_len = strlen($user_string);
			}
			if ($known_string_len !== $user_string_len) {
				$res = $known_string ^ $known_string; // use $known_string instead of $user_string to handle strings of diffrent length.
				$ret = 1; // set $ret to 1 to make sure false is returned
			} else {
				$res = $known_string ^ $user_string;
				$ret = 0;
			}
			for ($i = strlen($res) - 1; $i >= 0; $i--) {
				$ret |= ord($res[$i]);
			}
			return $ret === 0;
		}
	}
}

namespace authtools{
	use authtools\action\Action;
	use authtools\action\AuthenticateAction;
	use authtools\action\ConfiguredAction;
	use authtools\action\NativeAction;
	use authtools\input\InputHandler;
	use pocketmine\Player;
	use pocketmine\plugin\PluginBase;

	class AuthTools extends PluginBase{
		/** @var \SimpleAuth\SimpleAuth */
		private $simpleauth;
		/** @var AuthenticateSession[] */
		private $sessions = [];
		/** @var EventListener */
		private $eventListener;
		/** @var action\Action[] */
		private $actions = [];
		/** @var input\InputHandler[] */
		private $inputHandlers = [];
		public function onEnable(){
			$this->simpleauth = $this->getServer()->getPluginManager()->getPlugin("SimpleAuth");
			$this->saveDefaultConfig();
			$this->getServer()->getPluginManager()->registerEvents($this->eventListener = new EventListener($this), $this);
			$this->initNativeActions();
			$this->initInputHandlers();
			$this->reloadConfiguration();
		}
		private function initNativeActions(){
			$this->addNativeAction(new AuthenticateAction);
		}
		private function initInputHandlers(){

		}
		public function reloadConfiguration(){
			$this->reloadConfig();
			$this->reloadActions();
		}
		public function reloadActions(){
			$this->actions = array_filter($this->actions, function(Action $action){
				return $action instanceof NativeAction;
			});
			foreach($this->getConfig()->get("actions") as $name => $array){
				$this->actions[$name] = new ConfiguredAction($name, $array);
			}
			foreach($this->getConfiguredActions() as $action){
				$action->validate($this);
			}
		}
		/**
		 * @return ConfiguredAction[]
		 */
		public function getConfiguredActions(){
			return array_filter($this->actions, function(Action $action){
				return $action instanceof ConfiguredAction;
			});
		}
		public function getActions(){
			return $this->actions;
		}
		public function getAction($name){
			return isset($this->actions[$name]) ? $this->actions[$name] : null;
		}
		/**
		 * @param NativeAction $action
		 * @param bool $force
		 * @return bool whether it was originally set already
		 */
		public function addNativeAction(NativeAction $action, $force = false){
			$isset = isset($this->actions[$action->getName()]);
			if($isset and !$force){
				return true;
			}
			$this->actions[$action->getName()] = $action;
			return $isset;
		}
		public function getInputHandlers(){
			return $this->inputHandlers;
		}
		public function getInputHandler($name){
			return isset($this->inputHandlers[$name]) ? $this->inputHandlers[$name] : null;
		}
		public function addInputHandler(InputHandler $handler, $force){
			$isset = isset($this->inputHandlers[$handler->getName()]);
			if($isset and !$force){
				return true;
			}
			$this->inputHandlers[$handler->getName()] = $handler;
			return $isset;
		}
		public function openInternalSession(Player $player){
			$this->sessions[$this->pk($player)] = new AuthenticateSession($player);
		}
		public function closeInternalSession(Player $player){
			if(isset($this->sessions[$pk = $this->pk($player)])){
				$this->sessions[$pk]->close();
				return true;
			}
			return false;
		}
		private function pk(Player $player){
			return spl_object_hash($player);
		}
		public function checkAuth(Player $player, $password){
			$data = $this->simpleauth->getDataProvider()->getPlayer($player);
			if(!isset($data["hash"])){
				return -1;
			}
			/** @noinspection PhpUndefinedFunctionInspection */
			return \hash_equals($data["hash"], $this->hash(strtolower($player->getName()), $password)) ? 1 : 0;
		}
		private function hash($salt, $password){
			return bin2hex(hash("sha512", $password . $salt, true) ^ hash("whirlpool", $salt . $password, true));
		}
	}
}
