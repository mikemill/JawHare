<?php

namespace JawHare\Database;
abstract class Database
{
	protected $conns = array();
	protected $settings = array();
	protected $queries = array();

	public function __construct($settings)
	{
		$this->settings = $settings;

		$this->connect();
	}

	abstract protected function query($query, $replacements = array(), $conn = 'host');
	abstract public function insert($table, $columns, $data, $querytype = 'insert', $conn = 'insert');
	abstract protected function connect($set = 'all', $select_db = true, $reconnect = false);

	public function select($query, $replacements = array(), $conn = 'select', $rawsql = false)
	{
		if (!$rawsql)
		{
			list ($namespace, $queryns, $query) = explode(':', $query);

			if (!isset($this->queries[$namespace], $this->queries[$namespace][$queryns]))
				$this->load_queries($namespace, $queryns);

			$query = $this->queries[$namespace][$queryns][$query];
		}

		return $this->query($query, $replacements, $conn);
	}

	protected function load_queries($namespace, $queryns)
	{
		$classdir = str_replace(array(__NAMESPACE__, '\\'), array('', '/'), get_called_class());
		if ($namespace == 'JawHare')
			$queryfile = $this->settings['queries_dir'] . $classdir . '/' . $queryns . '.sql';
		else
			$queryfile = $this->settings['module_queries_dir'] . $classdir . '/' . $queryns . '.sql';

		$contents = file_get_contents($queryfile);

		preg_match_all('~/\* ?\[([0-9a-zA-Z_\-]+)\] begin ?\*/(.*?)/\* ?\[\1\] end ?\*/~s', $contents, $matches, PREG_SET_ORDER);

		$queries = array();

		foreach ($matches AS $match)
		{
			$queries[$match[1]] = trim($match[2]);
		}

		$this->queries[$namespace][$queryns] = $queries;
	}
}