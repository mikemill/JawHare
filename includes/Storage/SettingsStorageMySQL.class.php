<?php

namespace JawHare\Storage;

class SettingsStorageMySQL extends SettingsStorage
{
	public function save_settings($data)
	{
		$rows = array();

		foreach ($data AS $var => $value)
			$rows[] = array($var, $value);

		return $this->db->insert($this->table, $this->columns, $rows, 'replace');
	}

	public function delete_settings($variables)
	{
		return $this->db->query('
			DELETE FROM {sqlid:table}
			WHERE variable IN ({array_string:variables})',
			array(
				'table' => $this->table,
				'variables' => $variables,
			),
			'write'
		);
	}

	public function load_settings()
	{
		$results = $this->db->query('
			SELECT {array_identifiers:cols}
			FROM {sqlid:table}',
			array(
				'cols' => array_keys($this->columns),
				'table' => $this->table,
			)
		);

		$ret = array();

		while ($row = $results->assoc())
			$ret[$row['variable']] = $row['value'];

		return $ret;
	}
}