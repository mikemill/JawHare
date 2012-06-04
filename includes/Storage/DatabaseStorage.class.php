<?php

namespace JawHare\Storage;

/**
 * Generic storage from the database
 */
class DatabaseStorage
{
	/**
	 * Database object
	 * @var \JawHare\Database\Database
	 */
	protected $db = null;
	
	/**
	 * Cache object
	 * @var \JawHare\Cache\Cache
	 */
	protected $cache = null;
	
	public function __construct($db, $cache = null)
	{
		$this->db = $db;
		
		if ($cache === null)
			$this->cache = \JawHare\Cache();
		else
			$this->cache = $cache;
	}
}