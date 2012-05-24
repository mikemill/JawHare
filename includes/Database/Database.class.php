<?php

namespace JawHare\Database;
abstract class Database
{
	protected $conns = array();
	protected $settings = array();
	protected $dbtypename = '';
	protected $dbstorages = array();

	public function __construct($settings)
	{
		$this->settings = $settings;

		$this->connect();
	}

	abstract public function query($query, $replacements = array(), $conn = 'host');
	abstract public function insert($table, $columns, $data, $querytype = 'insert', $conn = 'write');
	abstract public function update($table, $columns, $data, $conn = 'write');
	abstract protected function connect($set = 'all', $select_db = true, $reconnect = false);

	public function select($query, $replacements = array(), $conn = 'select')
	{
		return $this->query($query, $replacements, $conn);
	}

	public function loadStorage($class, $namespace = '\\JawHare\\Storage\\')
	{
		$class = $namespace . $class . 'Storage' . $this->dbtypename;

		if (!isset($this->dbstorages[$class]))
			$this->dbstorages[$class] = new $class($this);

		return $this->dbstorages[$class];
	}
}