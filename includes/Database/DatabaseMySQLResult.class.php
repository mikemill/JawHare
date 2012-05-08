<?php

namespace JawHare\Database;
class DatabaseMySQLResult implements DatabaseResult
{
	protected $resource;
	protected $link;
	protected $sql;

	public function __construct($resource, $link, $sql = null)
	{
		$this->resource = $resource;
		$this->link = $link;
		$this->sql = $sql;
	}

	public function numrows()
	{
		return mysql_num_rows($this->resource);
	}

	public function assoc()
	{
		return mysql_fetch_assoc($this->resource);
	}

	public function row()
	{
		return mysql_fetch_row($this->resource);
	}

	public function affected_rows()
	{
		return mysql_affected_rows($this->link);
	}

	public function insert_id()
	{
		return mysql_insert_id($this->link);
	}
}
