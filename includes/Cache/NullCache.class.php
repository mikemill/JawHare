<?php

namespace JawHare\Cache;

/**
 * The Null Cache is a cache object that does nothing.
 * It doesn't maintain any type of caching and is only here to allow no cache
 * to exist without breaking logic.
 */
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
	public function error_code() { return -1; }
	public function error_message() { return 'No cache'; }
}
