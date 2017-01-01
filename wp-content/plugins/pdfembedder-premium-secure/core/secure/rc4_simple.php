<?php

/*
 * RC4 symmetric cipher using blocks
*/


class pdfembSimpleRC4 {
	
	protected $key;
	public function __construct($key) {
		$this->key = $key;
	}
	
	protected $s = null;
	protected $i, $j;
	
	protected function init_rc4() {
		$this->s = array();
		for ($i = 0; $i < 256; $i++) {
			$this->s[$i] = $i;
		}
		$j = 0;
		for ($i = 0; $i < 256; $i++) {
			$j = ($j + $this->s[$i] + ord($this->key[$i % strlen($this->key)])) % 256;
			$x = $this->s[$i];
			$this->s[$i] = $this->s[$j];
			$this->s[$j] = $x;
		}
		
		$this->i = 0;
		$this->j = 0;
	}
	
	public function rc4_encrypt_block($str) {
		if (is_null($this->s)) {
			$this->init_rc4();
		}
		
		$res = '';
		for ($y = 0; $y < strlen($str); $y++) {
			$this->i = ($this->i + 1) % 256;
			$this->j = ($this->j + $this->s[$this->i]) % 256;
			$x = $this->s[$this->i];
			$this->s[$this->i] = $this->s[$this->j];
			$this->s[$this->j] = $x;
			$res .= $str[$y] ^ chr($this->s[($this->s[$this->i] + $this->s[$this->j]) % 256]);
		}
		return $res;
	}
}

class pdfembDirectRC4 {

	protected $key;
	public function __construct($key) {
		$this->key = $key;
	}

	public function rc4_encrypt_block($str) {
		return $str;
	}
}


?>