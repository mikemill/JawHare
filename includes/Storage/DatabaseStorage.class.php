<?php

namespace JawHare\Storage;
class DatabaseStorage
{
	protected $db = null;
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