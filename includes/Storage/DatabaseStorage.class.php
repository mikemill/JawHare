<?php

namespace JawHare\Storage;
class DatabaseStorage
{
	protected $db = null;
	protected $columns = array();

	public function __construct($db)
	{
		$this->db = $db;
	}

	protected function colSQLID($col)
	{
		return '{' . $this->columns[$col] . ':' . $col . '}';
	}
}