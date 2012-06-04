<?php

namespace JawHare\Cache;

/**
 * The Cache interface.
 * Done as an interface instead of an abstract class because to allow extending base classes for the cache engine.
 */
interface Cache
{
	public function __construct($ini = null);
	/**
	 * Adds the key/value to the cache.
	 * Does not replace existing value
	 * @param string $key
	 * @param mixed $value
	 * @param int $expiration
	 * @return boolean true on sucess and false on failure
	 */
	public function add($key, $value, $expiration = 0);
	
	/**
	 * Removes the key from the cache
	 * @param string $key 
	 * @return boolean true on sucess and false on failure
	 */
	public function delete($key);
	
	/**
	 * Get the value for the key
	 * @param string $key
	 * @return mixed|false Returns false on error otherwise returns the value.
	 */
	public function get($key);
	
	/**
	 * Replace the current value for key.
	 * Fails if key is currently not set.
	 * @param string $key
	 * @param mixed $value
	 * @param int $expiration
	 * @return boolean true on sucess and false on failure
	 */
	public function replace($key, $value, $expiration = 0);
	
	/**
	 * Sets the key/value 
	 * @param string $key
	 * @param mixed $value
	 * @param int $expiration
	 * @return boolean true on sucess and false on failure
	 */
	public function set($key, $value, $expiration = null);
	
	/**
	 * Gets the error code for the last error.
	 * The return value will be specific to the actual cache implementation.
	 * This should only be used for logging purposes and not for logic flow.
	 * @return mixed The error code
	 */
	public function error_code();
	
	/**
	 * The string representing the last error that occured. 
	 * The return value will be specific to the actual cache implementation.
	 * This should only be used for logging purposes and not for logic flow.
	 * @return string The error message
	 */
	public function error_message();
}
