<?php

class bcrypt {
	private $rounds;
	
	public function __construct($rounds = 12) {
		if (CRYPT_BLOWFISH != 1) {
			throw new Exception("Bcrypt is not supported on this server, please see the following to learn more: http://php.net/crypt");
		}
		$this->rounds = $rounds;
	}
	
	public function genHash($password) {
		$hash = crypt($password, '$2y$' . $this->rounds . '$' . $this->genSalt());
		return $hash;
	}
	
	public function verify($password, $existingHash) {
		$hash = crypt($password, $existingHash);
		if ($hash === $existingHash) {
			return true;
		} else {
			return false;
		}
	}
	
	private function genSalt() {
		$string = str_shuffle(mt_rand());
		$salt = uniqid($string, true);
		return $salt;
	}
}
?>
