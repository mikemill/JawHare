<?php
namespace JawHare\Cache;

/**
 * Memcached cache object. 
 */
class CacheMemcached extends \Memcached implements Cache
{
	public function __construct($ini = null)
	{
		parent::__construct();
		
		if (!empty($ini))
			$this->addServer($ini['server'], $ini['port']);
	}

	public function set ($key, $value, $expiration = null)
	{
		// It is really easy to accidently not set an expiration time.  If this happens then have it expire in a day.
		return parent::set($key, $value, is_null($expiration) ? 86400 : $expiration);
	}

	public function error_code() { return $this->getResultCode(); }
	public function error_message() { return $this->getResultMessage(); }
}