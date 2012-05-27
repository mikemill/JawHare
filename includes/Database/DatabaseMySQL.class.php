<?php

namespace JawHare\Database;
class DatabaseMySQL extends Database
{
	protected $dbtypename = 'MySQL';

	public function __construct($settings)
	{
		$this->settings = $settings;

		$this->connect();
	}

	public function query($query, $replacements = array(), $conn = 'host')
	{
		$sql = preg_replace_callback('~\{([a-zA-Z_\-0-9]+):([a-zA-Z_\-0-9]+)\}~', function($matches) use ($replacements)
		{
			$type = $matches[1];
			$value = $replacements[$matches[2]];

			switch ($type)
			{
				case 'string':
					return '"' . mysql_real_escape_string((string) $value) . '"';
				case 'array_string':
					foreach ($value AS &$val)
						$val = '"' . mysql_real_escape_string((string) $val) . '"';
					unset($val);
					return implode(', ', $value);
				case 'sqlid':
					return '`' . (string) $value . '`';
				case 'array_identifiers':
					foreach ($value AS &$val)
						$val = '`' . (string) $val . '`';
					unset($val);
					return implode(', ', $value);
				case 'int':
					return (string) (int) $value;
				case 'bool':
					return ((bool) $value) ? '1' : '0';
			}

			return $matches[0];
		}, $query);

		$result = mysql_query($sql, $this->conns[$conn]);

		if (!$result)
			throw new DatabaseException($sql, mysql_error());

		return new DatabaseMySQLResult($result, $this->conns[$conn], $sql);
	}

	public function insert($table, $columns, $data, $querytype = 'insert', $conn = 'write')
	{
		$replacements = array(
			'table' => $table,
		);
		$columnnames = array_keys($columns);
		$columnnamesrev = array_flip($columnnames);
		$columntypes = array_values($columns);

		$colcount = count($columns);

		$sql = strtoupper($querytype) . ' INTO {sqlid:table} (';

		foreach ($columnnames AS $num => $colname)
		{
			$sql .= '{sqlid:col' . $num . '}' . ($num < $colcount - 1 ? ', ' : '');
			$replacements['col' . $num] = $colname;
		}

		$sql .= ') VALUES ';

		$datacount = count($data);
		$replacecount = 0;
		foreach ($data AS $num => $tuple)
		{
			$minisql = '(';
			foreach ($columntypes AS $colnum => $type)
			{
				$minisql .= '{' . $type . ':data' . $replacecount . '}'. ($colnum < $colcount - 1 ? ', ' : '');;
				$replacements['data' . $replacecount++] = $tuple[$colnum];
			}

			$minisql .= ')';
			$sql .= "\n$minisql";

			if ($num < $datacount - 1)
				$sql .= ',';
		}

		return $this->query($sql, $replacements, $conn);
	}

	public function update($table, $columns, $data, $conn = 'write')
	{
	}

	protected function connect($set = 'all', $select_db = true, $reconnect = false)
	{
		if ($set == 'all')
			$set = array('host', 'select', 'write');
		elseif (!is_array($set))
			$set = array($set);

		foreach ($set AS $type)
		{
			if (isset($this->settings[$type]))
			{
				$db_conn = mysql_connect($this->settings[$type], $this->settings['user'], $this->settings['password'], $reconnect);

				if ($select_db)
				{
					mysql_select_db($this->settings['db'], $db_conn);
				}

				$this->conns[$type] = $db_conn;
			}
			else
				$this->conns[$type] = $this->conns['host'];
		}
	}
}
