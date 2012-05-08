<?php

namespace JawHare;

class NullCache implements Cache
{
	public function __construct($ini = null)
	{
	}
	
	// Return failure values
	public function add($key, $value, $expiration = 0) {return false;}
	public function delete($key) {return false;}
	public function get($key) {return false;}
	public function replace($key, $value, $expiration = 0) {return false;}
	public function set($key, $value, $expiration = null) {return false;}
	public function getErrorCode() { return -1; }
	public function getErrorMessage() { return 'No cache'; }
}
