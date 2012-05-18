<?php

namespace JawHare\Database;
abstract class Database
{
	protected $conns = array();
	protected $settings = array();

	public function __construct($settings)
	{
		$this->settings = $settings;

		$this->connect();
	}

	abstract protected function query($query, $replacements = array(), $conn = 'host');
	abstract public function insert($table, $columns, $data, $querytype = 'insert', $conn = 'insert');
	abstract protected function connect($set = 'all', $select_db = true, $reconnect = false);

	public function select($query, $replacements = array(), $conn = 'select')
	{
		return $this->query($query, $replacements, $conn);
	}
}